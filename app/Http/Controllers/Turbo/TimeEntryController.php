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

    public function edit(TimeEntry $timeEntry)
    {
        return view('turbo::time-entries.edit', ['timeEntry' => $timeEntry]);
    }
}
