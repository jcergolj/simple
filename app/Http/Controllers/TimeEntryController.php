<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveTimeEntryRequest;
use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\ValueObjects\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

    public function store(SaveTimeEntryRequest $request)
    {
        $validated = $request->validated();

        $duration = null;
        if ($validated['end_time']) {
            $startTime = Carbon::parse($validated['start_time']);
            $endTime = Carbon::parse($validated['end_time']);
            $duration = max(0, $startTime->diffInSeconds($endTime));
        }

        $hourlyRate = null;
        if ($validated['hourly_rate_amount']) {
            $hourlyRate = new Money(
                amount: (float) $validated['hourly_rate_amount'],
                currency: $validated['hourly_rate_currency'] ?? 'USD'
            );
        }

        TimeEntry::create([
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'duration' => $duration,
            'notes' => $validated['notes'],
            'client_id' => $validated['client_id'],
            'project_id' => $validated['project_id'],
            'hourly_rate' => $hourlyRate,
        ]);

        InAppNotification::success(__('New time entry successfully created.'));

        return to_intended_route('time-entries.index');
    }

    public function update(SaveTimeEntryRequest $request, TimeEntry $timeEntry)
    {
        $validated = $request->validated();

        $duration = null;
        if ($validated['end_time']) {
            $startTime = Carbon::parse($validated['start_time']);
            $endTime = Carbon::parse($validated['end_time']);
            $duration = max(0, $startTime->diffInSeconds($endTime));
        }

        $hourlyRate = null;
        if ($validated['hourly_rate_amount']) {
            $hourlyRate = new Money(
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

        return to_intended_route('time-entries.index');
    }

    public function destroy(TimeEntry $timeEntry)
    {
        $timeEntry->delete();

        InAppNotification::success(__('Time entry successfully deleted.'));

        return to_intended_route('time-entries.index');
    }
}
