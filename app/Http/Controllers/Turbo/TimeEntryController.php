<?php

namespace App\Http\Controllers\Turbo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Turbo\StoreTimeEntryRequest;
use App\Http\Requests\Turbo\UpdateTimeEntryRequest;
use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\ValueObjects\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TimeEntryController extends Controller
{
    public function create()
    {
        return view('turbo::time-entries.create');
    }

    public function store(StoreTimeEntryRequest $request)
    {
        $validated = $request->validated();

        $duration = null;
        if ($validated['end_time']) {
            $startTime = Carbon::parse($validated['start_time']);
            $endTime = Carbon::parse($validated['end_time']);
            $duration = max(0, $startTime->diffInSeconds($endTime));
        }

        $hourlyRate = null;
        if (! empty($validated['hourly_rate_amount'])) {
            $hourlyRate = Money::fromDecimal(
                amount: (float) $validated['hourly_rate_amount'],
                currency: $validated['hourly_rate_currency'] ?? 'USD'
            );
        }

        $timeEntry = TimeEntry::create([
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'duration' => $duration,
            'notes' => $validated['notes'],
            'client_id' => $validated['client_id'],
            'project_id' => $validated['project_id'],
            'hourly_rate' => $hourlyRate,
        ]);

        Log::channel('time-entries')->info('time-entry-created', $timeEntry->toArray());

        // Fetch updated list with filters applied
        $query = TimeEntry::with(['client', 'project']);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('date_from')) {
            $query->where('start_time', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('start_time', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $timeEntries = $query->latest('start_time')->paginate(20);

        $clients = Client::all();
        $projects = Project::with('client')->get();

        return response()
            ->view('turbo::time-entries.store', [
                'timeEntries' => $timeEntries,
                'clients' => $clients,
                'projects' => $projects,
            ])
            ->header('Content-Type', 'text/vnd.turbo-stream.html');
    }

    public function edit(TimeEntry $timeEntry, Request $request)
    {
        if ($request->has('recent') || $request->header('turbo-frame') === "recent-entry-{$timeEntry->id}") {
            return view('turbo::time-entries.edit-recent', ['timeEntry' => $timeEntry]);
        }

        return view('turbo::time-entries.edit', ['timeEntry' => $timeEntry]);
    }

    public function update(UpdateTimeEntryRequest $request, TimeEntry $timeEntry)
    {
        $validated = $request->validated();

        $duration = null;
        if ($validated['end_time']) {
            $startTime = Carbon::parse($validated['start_time']);
            $endTime = Carbon::parse($validated['end_time']);
            $duration = max(0, $startTime->diffInSeconds($endTime));
        }

        $hourlyRate = null;
        if (! empty($validated['hourly_rate_amount'])) {
            $hourlyRate = Money::fromDecimal(
                amount: (float) $validated['hourly_rate_amount'],
                currency: $validated['hourly_rate_currency'] ?? 'USD'
            );
        }

        $timeEntry->update([
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'duration' => $duration,
            'notes' => $validated['notes'],
            'client_id' => $validated['client_id'],
            'project_id' => $validated['project_id'],
            'hourly_rate' => $hourlyRate,
        ]);

        Log::channel('time-entries')->info('time-entry-updated', $timeEntry->fresh()->toArray());

        // Check if this update came from the recent entries section
        if ($request->header('turbo-frame') === "recent-entry-{$timeEntry->id}") {
            // Load fresh data for the recent entries
            $recentEntries = TimeEntry::with(['client', 'project'])
                ->latest('start_time')
                ->limit(10)
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

            // Fetch running timer to maintain correct button states
            $runningTimer = TimeEntry::whereNull('end_time')->first();

            return response()
                ->view('timer-sessions.recent-entry-update', [
                    'timeEntry' => $timeEntry->fresh(['client', 'project']),
                    'recentEntries' => $recentEntries,
                    'totalHours' => $totalHours,
                    'totalAmount' => $earnings['totalAmount'],
                    'weeklyEarnings' => $earnings['weeklyEarnings'],
                    'runningTimer' => $runningTimer,
                ])
                ->header('Content-Type', 'text/vnd.turbo-stream.html');
        }

        // Fetch updated list with filters applied
        $query = TimeEntry::with(['client', 'project']);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('date_from')) {
            $query->where('start_time', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('start_time', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $timeEntries = $query->latest('start_time')->paginate(20);

        $clients = Client::all();
        $projects = Project::with('client')->get();

        return response()
            ->view('turbo::time-entries.update', [
                'timeEntry' => $timeEntry->fresh(['client', 'project']),
                'timeEntries' => $timeEntries,
                'clients' => $clients,
                'projects' => $projects,
            ])
            ->header('Content-Type', 'text/vnd.turbo-stream.html');
    }
}
