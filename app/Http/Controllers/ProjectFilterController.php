<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectFilterController extends Controller
{
    public function __invoke(Request $request)
    {
        $clientId = $request->input('client_id');
        $selectedProjectId = $request->input('selected_project_id');

        $projects = collect();

        if ($clientId) {
            // Optimize query with select to reduce data transfer
            $projects = Project::select('id', 'name', 'client_id')
                ->where('client_id', $clientId)
                ->orderBy('name')
                ->get();
        }

        // Determine the frame ID from the Turbo-Frame header
        $frameId = $request->header('Turbo-Frame', 'project-filter-desktop');

        return view('turbo::project-filter.index', [
            'projects' => $projects,
            'selectedProjectId' => $selectedProjectId,
            'clientId' => $clientId,
            'frameId' => $frameId,
        ]);
    }
}
