<?php

namespace App\Http\Controllers\Turbo;

use App\Http\Controllers\Controller;
use App\Models\Project;

class ProjectController extends Controller
{
    public function create()
    {
        return view('turbo::projects.create');
    }

    public function edit(Project $project)
    {
        return view('turbo::projects.edit', ['project' => $project]);
    }
}
