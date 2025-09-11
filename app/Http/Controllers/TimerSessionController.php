<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class TimerSessionController extends Controller
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
            return view('timer-sessions.running', ['runningTimer' => $runningTimer, 'clients' => $clients, 'projects' => $projects]);
        }

        return view('timer-sessions.start', ['clients' => $clients, 'projects' => $projects, 'lastEntry' => $lastEntry]);
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

        InAppNotification::success(__('Timer started successfully!'));

        $clients = Client::all();
        $projects = Project::with('client')->get();

        return view('timer-sessions.running', ['timeEntry' => $timeEntry, 'clients' => $clients, 'projects' => $projects]);
    }

    public function update(Request $request)
    {
        $runningEntry = TimeEntry::whereNull('end_time')->first();

        if (! $runningEntry) {
            InAppNotification::error(__('No active timer found to stop.'));

            return $this->show();
        }

        $runningEntry->update([
            'end_time' => now(),
            'duration' => max(0, $runningEntry->start_time->diffInSeconds(now())),
        ]);

        InAppNotification::success(__('Timer stopped! :duration tracked.', [
            'duration' => $runningEntry->getFormattedDuration(),
        ]));

        $clients = Client::all();
        $projects = Project::with('client')->get();
        $lastEntry = $runningEntry;

        // Get fresh recent entries to update the dashboard
        $recentEntries = TimeEntry::with(['client', 'project'])
            ->latest('start_time')
            ->limit(5)
            ->get();

        // Return a response that updates multiple Turbo Frames
        return response()
            ->view('timer-sessions.multiple-updates', ['clients' => $clients, 'projects' => $projects, 'lastEntry' => $lastEntry, 'recentEntries' => $recentEntries])
            ->header('Content-Type', 'text/vnd.turbo-stream.html');
    }

    public function destroy()
    {
        $runningEntry = TimeEntry::whereNull('end_time')->first();

        if (! $runningEntry) {
            InAppNotification::error(__('No active timer found to cancel.'));

            return $this->show();
        }

        $runningEntry->delete();

        InAppNotification::success(__('Timer cancelled successfully.'));

        return $this->show();
    }
}
