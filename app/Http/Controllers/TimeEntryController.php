<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\ValueObjects\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class TimeEntryController extends Controller
{
    public function index(Request $request)
    {
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

        redirect()->redirectIfLastPageEmpty($request, $timeEntries);

        $clients = Client::all();
        $projects = Project::with('client')->get();

        return view('time-entries.index', ['timeEntries' => $timeEntries, 'clients' => $clients, 'projects' => $projects]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_time' => ['required', 'date'],
                'end_time' => ['required', 'date', 'after:start_time'],
                'duration' => ['nullable', 'integer', 'min:0'],
                'notes' => ['nullable', 'string'],
                'client_id' => ['nullable', 'exists:clients,id'],
                'project_id' => ['nullable', 'exists:projects,id'],
                'hourly_rate_amount' => ['nullable', 'numeric', 'min:0'],
                'hourly_rate_currency' => 'required_with:hourly_rate_amount|string|in:'.implode(',', array_column(\App\Enums\Currency::cases(), 'value')),
            ], [
                'start_time.required' => 'Start time is required.',
                'start_time.date' => 'Start time must be a valid date.',
                'end_time.date' => 'End time must be a valid date.',
                'end_time.after' => 'End time must be after start time.',
                'duration.integer' => 'Duration must be a valid number.',
                'duration.min' => 'Duration must be at least 0.',
                'client_id.exists' => 'Selected client does not exist.',
                'project_id.exists' => 'Selected project does not exist.',
                'hourly_rate_amount.numeric' => 'Hourly rate must be a valid number.',
                'hourly_rate_amount.min' => 'Hourly rate must be at least 0.',
                'hourly_rate_currency.required_with' => 'Currency is required when hourly rate is specified.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception->redirectTo(route('turbo.time-entries.create'));
        }

        $duration = null;
        if ($validated['end_time']) {
            $startTime = Carbon::parse($validated['start_time']);
            $endTime = Carbon::parse($validated['end_time']);
            $duration = max(0, $startTime->diffInSeconds($endTime));
        }

        $hourlyRate = null;
        if ($validated['hourly_rate_amount']) {
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

        InAppNotification::success(__('New time entry successfully created.'));

        Log::channel('time-entries')->info('time-entry-created', $timeEntry->toArray());

        return to_route('time-entries.index');
    }

    public function update(Request $request, TimeEntry $timeEntry)
    {
        try {
            $validated = $request->validate([
                'start_time' => ['required', 'date'],
                'end_time' => ['nullable', 'date', 'after:start_time'],
                'duration' => ['nullable', 'integer', 'min:0'],
                'notes' => ['nullable', 'string'],
                'client_id' => ['nullable', 'exists:clients,id'],
                'project_id' => ['nullable', 'exists:projects,id'],
                'hourly_rate_amount' => ['nullable', 'numeric', 'min:0'],
                'hourly_rate_currency' => 'required_with:hourly_rate_amount|string|in:'.implode(',', array_column(\App\Enums\Currency::cases(), 'value')),
            ], [
                'start_time.required' => 'Start time is required.',
                'start_time.date' => 'Start time must be a valid date.',
                'end_time.date' => 'End time must be a valid date.',
                'end_time.after' => 'End time must be after start time.',
                'duration.integer' => 'Duration must be a valid number.',
                'duration.min' => 'Duration must be at least 0.',
                'client_id.exists' => 'Selected client does not exist.',
                'project_id.exists' => 'Selected project does not exist.',
                'hourly_rate_amount.numeric' => 'Hourly rate must be a valid number.',
                'hourly_rate_amount.min' => 'Hourly rate must be at least 0.',
                'hourly_rate_currency.required_with' => 'Currency is required when hourly rate is specified.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            // Redirect back to the correct edit form based on request context
            if ($request->header('turbo-frame') && str_contains($request->header('turbo-frame'), 'recent-entry-')) {
                // This is from a recent entry edit
                throw $exception->redirectTo(route('dashboard'));
            } else {
                // This is from the main time entries edit
                throw $exception->redirectTo(route('turbo.time-entries.edit', $timeEntry));
            }
        }

        $duration = null;
        if ($validated['end_time']) {
            $startTime = Carbon::parse($validated['start_time']);
            $endTime = Carbon::parse($validated['end_time']);
            $duration = max(0, $startTime->diffInSeconds($endTime));
        }

        $hourlyRate = null;
        if ($validated['hourly_rate_amount']) {
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

        InAppNotification::success(__('Time entry successfully updated.'));

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

            Log::channel('time-entries')->info('time-entry-updated', $timeEntry->fresh()->toArray());

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

        return to_route('time-entries.index');
    }

    public function destroy(Request $request, TimeEntry $timeEntry)
    {
        $timeEntry->delete();

        InAppNotification::success(__('Time entry successfully deleted.'));

        if ($request->is_recent) {
            return to_route('dashboard');
        }

        return to_intended_route('time-entries.index');
    }
}
