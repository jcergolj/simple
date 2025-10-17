<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\Services\WeeklyEarningsCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $weeklyEntries = TimeEntry::with(['client', 'project'])
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->whereNotNull('end_time')
            ->get();

        $totalHours = $weeklyEntries->sum('duration') / 3600;

        $earnings = WeeklyEarningsCalculator::calculate($weeklyEntries);

        $recentEntries = TimeEntry::with(['client', 'project'])
            ->latest('start_time')
            ->limit(5)
            ->get();

        $lastEntry = $recentEntries->first();

        $runningTimer = TimeEntry::whereNull('end_time')->first();

        $preselectedClientId = $request->get('client_id', $lastEntry?->client_id);
        $preselectedClientName = $request->get('client_name', $lastEntry?->client?->name);
        $preselectedProjectId = $request->get('project_id', $lastEntry?->project_id);
        $preselectedProjectName = $request->get('project_name', $lastEntry?->project?->name);

        return view('dashboard', [
            'totalHours' => $totalHours,
            'totalAmount' => $earnings['totalAmount'],
            'weeklyEarnings' => $earnings['weeklyEarnings'],
            'recentEntries' => $recentEntries,
            'lastEntry' => $lastEntry,
            'runningTimer' => $runningTimer,
            'preselectedClientId' => $preselectedClientId,
            'preselectedClientName' => $preselectedClientName,
            'preselectedProjectId' => $preselectedProjectId,
            'preselectedProjectName' => $preselectedProjectName,
        ]);
    }
}
