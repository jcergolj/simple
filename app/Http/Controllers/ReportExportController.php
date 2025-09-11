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
        $totalEarnings = $timeEntries->reduce(function ($carry, $entry) {
            $earnings = $entry->calculateEarnings();

            return $carry + ($earnings ? $earnings->amount / 100 : 0);
        }, 0);

        $projectTotals = $timeEntries->groupBy('project_id')->map(function ($entries) {
            $firstEntry = $entries->first();
            $project = $firstEntry->project;

            if ($project && ! $project->relationLoaded('client')) {
                $project->load('client');
            }

            $hours = $entries->sum('duration') / 3600;
            $earnings = $entries->reduce(function ($carry, $entry) {
                $earning = $entry->calculateEarnings();

                return $carry + ($earning ? $earning->amount / 100 : 0);
            }, 0);

            return [
                'project' => $project,
                'hours' => $hours,
                'earnings' => $earnings,
                'entry_count' => $entries->count(),
            ];
        })->sortByDesc('earnings')->values();

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
                $entry->getEffectiveHourlyRate()?->formatted() ?? '',
                $earnings?->formatted() ?? ''
            );
        }

        $csv .= sprintf(
            "\n%s,%s,%s,%.2f,%s,%s,%s,%s,$%.2f\n",
            '',
            '',
            '',
            $totalHours,
            '',
            '',
            '',
            'TOTAL',
            $totalEarnings
        );

        if ($projectTotals->isNotEmpty()) {
            $csv .= "\n\nSUMMARY BY PROJECT\n";
            $csv .= "Project,Client,Entries,Hours,Earnings\n";

            foreach ($projectTotals as $projectTotal) {
                $csv .= sprintf(
                    "%s,%s,%d,%.2f,$%.2f\n",
                    $projectTotal['project']->name ?? 'No Project',
                    $projectTotal['project']->client->name ?? 'No Client',
                    $projectTotal['entry_count'],
                    $projectTotal['hours'],
                    $projectTotal['earnings']
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
