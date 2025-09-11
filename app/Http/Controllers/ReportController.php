<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
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

        // Calculate totals by project
        $projectTotals = $timeEntries->groupBy('project_id')->map(function ($entries) {
            $firstEntry = $entries->first();
            $project = $firstEntry->project;

            // Eager load client relationship if not already loaded
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

        $clients = Client::all();
        $projects = Project::all();

        return view('reports.index', [
            'timeEntries' => $timeEntries,
            'totalHours' => $totalHours,
            'totalEarnings' => $totalEarnings,
            'projectTotals' => $projectTotals,
            'clients' => $clients,
            'projects' => $projects,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
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
