<?php

namespace App\Http\Controllers;

use App\Models\Client;
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

        if ($request->filled('date_from')) {
            $query->where('start_time', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('start_time', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $timeEntries = $query->latest('start_time')->get();

        $totalHours = $timeEntries->sum('duration') / 3600;
        $totalEarnings = $timeEntries->reduce(function ($carry, $entry) {
            $earnings = $entry->calculateEarnings();

            return $carry + ($earnings ? $earnings->amount : 0);
        }, 0);

        $clients = Client::all();

        return view('reports.index', ['timeEntries' => $timeEntries, 'totalHours' => $totalHours, 'totalEarnings' => $totalEarnings, 'clients' => $clients]);
    }

    public function export(Request $request)
    {
        $query = TimeEntry::with(['client', 'project'])->whereNotNull('end_time');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('date_from')) {
            $query->where('start_time', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('start_time', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $timeEntries = $query->latest('start_time')->get();

        // Calculate totals
        $totalHours = $timeEntries->sum('duration') / 3600;
        $totalEarnings = $timeEntries->reduce(function ($carry, $entry) {
            $earnings = $entry->calculateEarnings();

            return $carry + ($earnings ? $earnings->amount : 0);
        }, 0);

        $csv = "Date,Start Time,End Time,Duration (Hours),Client,Project,Notes,Hourly Rate,Earnings\n";

        foreach ($timeEntries as $entry) {
            $earnings = $entry->calculateEarnings();
            $csv .= sprintf(
                "%s,%s,%s,%.2f,%s,%s,%s,%s,%s\n",
                $entry->start_time->format('Y-m-d'),
                $entry->start_time->format('H:i'),
                $entry->end_time?->format('H:i') ?? '',
                $entry->duration / 3600,
                $entry->client->name ?? '',
                $entry->project->name ?? '',
                str_replace(['"', ','], ['""', ''], $entry->notes ?? ''),
                $entry->getEffectiveHourlyRate()?->formatted() ?? '',
                $earnings?->formatted() ?? ''
            );
        }

        // Add totals row
        $csv .= sprintf(
            "\n%s,%s,%s,%.2f,%s,%s,%s,%s,$%.2f\n",
            '',
            '',
            '',
            $totalHours,
            '',
            '',
            '',
            'TOTAL',
            $totalEarnings
        );

        $filename = 'time_report_'.now()->format('Y_m_d_H_i_s').'.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
