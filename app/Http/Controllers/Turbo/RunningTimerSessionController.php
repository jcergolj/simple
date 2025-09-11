<?php

namespace App\Http\Controllers\Turbo;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RunningTimerSessionController extends Controller
{
    public function show()
    {
        $runningTimer = TimeEntry::whereNull('end_time')->first();
        $clients = Client::all();
        $projects = Project::with('client')->get();

        $lastEntry = TimeEntry::whereNotNull('end_time')
            ->latest('end_time')
            ->first();

        if ($runningTimer) {
            return view('turbo::timer-sessions.running', ['runningTimer' => $runningTimer, 'clients' => $clients, 'projects' => $projects]);
        }

        return view('turbo::timer-sessions.start', ['clients' => $clients, 'projects' => $projects, 'lastEntry' => $lastEntry]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
        ], [
            'client_id.exists' => __('The selected client is invalid.'),
            'project_id.exists' => __('The selected project is invalid.'),
        ]);

        TimeEntry::whereNull('end_time')->update([
            'end_time' => now(),
            'duration' => DB::raw('strftime("%s", "now") - strftime("%s", start_time)'),
        ]);

        $timeEntry = TimeEntry::create([
            'start_time' => now(),
            'client_id' => $request->client_id,
            'project_id' => $request->project_id,
        ]);

        Log::channel('time-entries')->info('time-entry-auto-created', $timeEntry->toArray());

        $clients = Client::all();
        $projects = Project::with('client')->get();

        // Load the timeEntry with relationships for display
        $timeEntry->load(['client', 'project']);

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
            ->view('turbo::timer-sessions.started', [
                'timeEntry' => $timeEntry,
                'clients' => $clients,
                'projects' => $projects,
                'recentEntries' => $recentEntries,
                'totalHours' => $totalHours,
                'totalAmount' => $earnings['totalAmount'],
                'weeklyEarnings' => $earnings['weeklyEarnings'],
            ])
            ->header('Content-Type', 'text/vnd.turbo-stream.html');
    }

    public function edit()
    {
        $runningTimer = TimeEntry::whereNull('end_time')->first();

        if (! $runningTimer) {
            return $this->show();
        }

        $clients = Client::all();
        $projects = Project::with('client')->get();

        return view('turbo::timer-sessions.edit', ['runningTimer' => $runningTimer, 'clients' => $clients, 'projects' => $projects]);
    }

    public function update(Request $request)
    {
        $runningEntry = TimeEntry::whereNull('end_time')->first();

        if (! $runningEntry) {
            return to_route('turbo.running-timer-session.show');
        }

        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'start_time' => 'required|date|before_or_equal:now',
        ], [
            'client_id.exists' => __('The selected client is invalid.'),
            'project_id.exists' => __('The selected project is invalid.'),
            'start_time.required' => __('Start time is required.'),
            'start_time.date' => __('Start time must be a valid date.'),
            'start_time.before_or_equal' => __('Start time cannot be in the future.'),
        ]);

        $runningEntry->update([
            'client_id' => $validated['client_id'],
            'project_id' => $validated['project_id'],
            'start_time' => $validated['start_time'],
        ]);

        Log::channel('time-entries')->info('timer-session-updated', $runningEntry->fresh()->toArray());

        $clients = Client::all();
        $projects = Project::with('client')->get();

        // Get fresh recent entries to update the dashboard since we modified a running timer
        $recentEntries = TimeEntry::with(['client', 'project'])
            ->latest('start_time')
            ->limit(5)
            ->get();

        // Calculate updated weekly metrics (for consistency with stop method)
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $weeklyEntries = TimeEntry::with(['client', 'project'])
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->whereNotNull('end_time')
            ->get();

        $totalHours = $weeklyEntries->sum('duration') / 3600;

        $earnings = \App\Services\WeeklyEarningsCalculator::calculate($weeklyEntries);

        // Return turbo-stream response to update timer widget and recent entries
        return response()
            ->view('turbo::timer-sessions.running-updated', [
                'runningTimer' => $runningEntry->fresh(['client', 'project']),
                'clients' => $clients,
                'projects' => $projects,
                'recentEntries' => $recentEntries,
                'totalHours' => $totalHours,
                'totalAmount' => $earnings['totalAmount'],
                'weeklyEarnings' => $earnings['weeklyEarnings'],
            ])
            ->header('Content-Type', 'text/vnd.turbo-stream.html');
    }

    public function destroy()
    {
        $runningEntry = TimeEntry::whereNull('end_time')->first();

        if ($runningEntry) {
            $runningEntry->delete();
        }

        $clients = Client::all();
        $projects = Project::with('client')->get();

        // Get the last completed entry for preselection
        $lastEntry = TimeEntry::whereNotNull('end_time')
            ->latest('end_time')
            ->first();

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
