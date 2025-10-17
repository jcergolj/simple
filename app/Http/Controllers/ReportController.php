<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = TimeEntry::with(['client', 'project'])->whereNotNull('end_time');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Handle predefined date ranges
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        if ($request->filled('date_range')) {
            [$dateFrom, $dateTo] = $this->getDateRange($request->date_range);
        }

        // Parse dates to Carbon instances
        $dateFromCarbon = $dateFrom ? Carbon::parse($dateFrom) : null;
        $dateToCarbon = $dateTo ? Carbon::parse($dateTo) : null;

        if ($dateFromCarbon instanceof \Carbon\Carbon) {
            $query->where('start_time', '>=', $dateFromCarbon);
        }

        if ($dateToCarbon instanceof \Carbon\Carbon) {
            $query->where('start_time', '<=', $dateToCarbon->endOfDay());
        }

        $timeEntries = $query->latest('start_time')->get();

        $totalHours = $timeEntries->sum('duration') / 3600;

        // Group earnings by currency for proper totals
        $earningsByCurrency = $timeEntries->map(fn ($entry) => $entry->calculateEarnings())->filter()->groupBy(fn ($earnings) => $earnings->currency->value)->map(function ($currencyEarnings) {
            $currency = $currencyEarnings->first()->currency;
            $totalAmount = $currencyEarnings->sum('amount');

            return new \App\ValueObjects\Money($totalAmount, $currency);
        });

        // Calculate totals by project
        $projectTotals = $timeEntries->groupBy('project_id')->map(function ($entries) {
            $firstEntry = $entries->first();
            $project = $firstEntry->project;

            // Eager load client relationship if not already loaded
            if ($project && ! $project->relationLoaded('client')) {
                $project->load('client');
            }

            $hours = $entries->sum('duration') / 3600;

            // Group project earnings by currency
            $projectEarningsByCurrency = $entries->map(fn ($entry) => $entry->calculateEarnings())->filter()->groupBy(fn ($earnings) => $earnings->currency->value)->map(function ($currencyEarnings) {
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

        $clients = Client::all();
        $projects = Project::all();

        return view('reports.index', [
            'timeEntries' => $timeEntries,
            'totalHours' => $totalHours,
            'earningsByCurrency' => $earningsByCurrency,
            'projectTotals' => $projectTotals,
            'clients' => $clients,
            'projects' => $projects,
            'dateFrom' => $dateFromCarbon,
            'dateTo' => $dateToCarbon,
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
