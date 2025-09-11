<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = TimeEntry::with(['client', 'project'])->whereNotNull('end_time');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        if ($request->filled('date_range')) {
            [$dateFrom, $dateTo] = $this->getDateRange($request->date_range);
        }

        if ($dateFrom) {
            $query->where('start_time', '>=', Carbon::parse($dateFrom));
        }

        if ($dateTo) {
            $query->where('start_time', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        $timeEntries = $query->latest('start_time')->get();

        $totalHours = $timeEntries->sum('duration') / 3600;

        // Group earnings by currency for proper totals
        $earningsByCurrency = $timeEntries->map(function ($entry) {
            return $entry->calculateEarnings();
        })->filter()->groupBy(function ($earnings) {
            return $earnings->currency->value;
        })->map(function ($currencyEarnings) {
            $currency = $currencyEarnings->first()->currency;
            $totalAmount = $currencyEarnings->sum('amount');

            return new \App\ValueObjects\Money($totalAmount, $currency);
        });

        $projectTotals = $timeEntries->groupBy('project_id')->map(function ($entries) {
            $firstEntry = $entries->first();
            $project = $firstEntry->project;

            if ($project && ! $project->relationLoaded('client')) {
                $project->load('client');
            }

            $hours = $entries->sum('duration') / 3600;

            // Group project earnings by currency
            $projectEarningsByCurrency = $entries->map(function ($entry) {
                return $entry->calculateEarnings();
            })->filter()->groupBy(function ($earnings) {
                return $earnings->currency->value;
            })->map(function ($currencyEarnings) {
                $currency = $currencyEarnings->first()->currency;
                $totalAmount = $currencyEarnings->sum('amount');

                return new \App\ValueObjects\Money($totalAmount, $currency);
            });

            // For sorting, use the first currency's amount (could be improved with currency conversion)
            $earningsForSorting = $projectEarningsByCurrency->first()?->toDecimal() ?? 0;

            return [
                'project' => $project,
                'hours' => $hours,
                'earningsByCurrency' => $projectEarningsByCurrency,
                'earningsForSorting' => $earningsForSorting,
                'entry_count' => $entries->count(),
            ];
        })->sortByDesc('earningsForSorting')->values();

        $csv = "Date,Start Time,End Time,Duration (Hours),Client,Project,Notes,Hourly Rate,Earnings\n";

        foreach ($timeEntries as $entry) {
            $earnings = $entry->calculateEarnings();
            $csv .= sprintf(
                "%s,%s,%s,%.2f,%s,%s,%s,%s,%s\n",
                $entry->start_time->format('Y-m-d'),
                $entry->start_time->format('H:i'),
                $entry->end_time?->format('H:i') ?? '',
                $entry->duration / 3600,
                $entry->client->name ?? '',
                $entry->project->name ?? '',
                str_replace(['"', ','], ['""', ''], $entry->notes ?? ''),
                $entry->getEffectiveHourlyRate()?->formattedForCsv() ?? '',
                $earnings?->formattedForCsv() ?? ''
            );
        }

        $csv .= "\n";

        // Add total earnings by currency
        foreach ($earningsByCurrency as $currencyCode => $totalMoney) {
            $csv .= sprintf(
                "%s,%s,%s,%.2f,%s,%s,%s,%s,%s\n",
                '',
                '',
                '',
                $totalHours,
                '',
                '',
                '',
                "TOTAL ($currencyCode)",
                $totalMoney->formattedForCsv()
            );
        }

        if ($projectTotals->isNotEmpty()) {
            $csv .= "\n\nSUMMARY BY PROJECT\n";
            $csv .= "Project,Client,Entries,Hours,Earnings\n";

            foreach ($projectTotals as $projectTotal) {
                // Format project earnings by currency
                $earningsDisplay = $projectTotal['earningsByCurrency']->map(function ($money) {
                    return $money->formattedForCsv();
                })->implode(' + ');

                $csv .= sprintf(
                    "%s,%s,%d,%.2f,%s\n",
                    $projectTotal['project']->name ?? 'No Project',
                    $projectTotal['project']->client->name ?? 'No Client',
                    $projectTotal['entry_count'],
                    $projectTotal['hours'],
                    $earningsDisplay ?: '0'
                );
            }
        }

        $filename = 'time_report_'.now()->format('Y_m_d_H_i_s').'.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    protected function getDateRange(string $range): array
    {
        $now = Carbon::now();

        return match ($range) {
            'this_week' => [
                $now->copy()->startOfWeek(),
                $now->copy(),
            ],
            'last_week' => [
                $now->copy()->subWeek()->startOfWeek(),
                $now->copy()->subWeek()->endOfWeek(),
            ],
            'this_month' => [
                $now->copy()->startOfMonth(),
                $now->copy(),
            ],
            'last_month' => [
                $now->copy()->subMonth()->startOfMonth(),
                $now->copy()->subMonth()->endOfMonth(),
            ],
            'this_year' => [
                $now->copy()->startOfYear(),
                $now->copy(),
            ],
            'last_year' => [
                $now->copy()->subYear()->startOfYear(),
                $now->copy()->subYear()->endOfYear(),
            ],
            default => [null, null],
        };
    }
}
