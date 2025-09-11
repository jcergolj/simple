<?php

namespace App\Http\Controllers\Turbo;

use App\Http\Controllers\Controller;
use App\Models\TimeEntry;

class TimeEntryController extends Controller
{
    public function create()
    {
        return view('turbo::time-entries.create');
    }

    public function edit(TimeEntry $timeEntry, \Illuminate\Http\Request $request)
    {
        if ($request->has('recent') || $request->header('turbo-frame') === "recent-entry-{$timeEntry->id}") {
            return view('turbo::time-entries.edit-recent', ['timeEntry' => $timeEntry]);
        }

        return view('turbo::time-entries.edit', ['timeEntry' => $timeEntry]);
    }
}
