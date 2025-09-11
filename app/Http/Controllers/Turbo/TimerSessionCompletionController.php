<?php

namespace App\Http\Controllers\Turbo;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TimerSessionCompletionController extends Controller
{
    /** Complete (stop) a running timer session. */
    public function __invoke(Request $request)
    {
        $runningEntry = TimeEntry::whereNull('end_time')->first();

        if (! $runningEntry) {
            $clients = Client::all();
            $projects = Project::with('client')->get();

            $lastEntry = TimeEntry::whereNotNull('end_time')
                ->latest('end_time')
                ->first();

            return response()
                ->view('turbo::timer-sessions.start', [
                    'clients' => $clients,
                    'projects' => $projects,
                    'lastEntry' => $lastEntry,
                ])
                ->header('Content-Type', 'text/vnd.turbo-stream.html');
        }

        $runningEntry->update([
            'end_time' => now(),
            'duration' => max(0, $runningEntry->start_time->diffInSeconds(now())),
        ]);

        $clients = Client::all();
        $projects = Project::with('client')->get();

        $lastEntry = $runningEntry->fresh(['client', 'project']);

        Log::channel('time-entries')->info('time-entry-auto-stopped', $lastEntry->toArray());

        // Get fresh recent entries to update the dashboard
        $recentEntries = TimeEntry::with(['client', 'project'])
            ->latest('start_time')
            ->limit(5)
            ->get();

        // Calculate updated weekly metrics
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $weeklyEntries = TimeEntry::with(['client', 'project'])
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->whereNotNull('end_time')
            ->get();

        $totalHours = $weeklyEntries->sum('duration') / 3600;

        $earnings = \App\Services\WeeklyEarningsCalculator::calculate($weeklyEntries);

        return response()
            ->view('turbo::timer-sessions.stopped', [
                'clients' => $clients,
                'projects' => $projects,
                'lastEntry' => $lastEntry,
                'recentEntries' => $recentEntries,
                'totalHours' => $totalHours,
                'totalAmount' => $earnings['totalAmount'],
                'weeklyEarnings' => $earnings['weeklyEarnings'],
            ])
            ->header('Content-Type', 'text/vnd.turbo-stream.html');
    }
}
