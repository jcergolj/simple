<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
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
