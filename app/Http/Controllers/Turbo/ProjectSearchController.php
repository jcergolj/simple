<?php

namespace App\Http\Controllers\Turbo;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->input('q', '');
        $clientId = $request->input('client_id');

        if (empty($query) || strlen((string) $query) < 2) {
            return response('', 200);
        }

        $projectsQuery = Project::where('name', 'like', '%'.$query.'%')
            ->with('client');

        if ($clientId) {
            $projectsQuery->where('client_id', $clientId);
        }

        $projects = $projectsQuery->limit(10)->get();

        return view('turbo::projects-search.index', ['projects' => $projects, 'clientId' => $clientId]);

    }
}
