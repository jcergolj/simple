<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('client')->withCount('timeEntries');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function (\Illuminate\Contracts\Database\Query\Builder $q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhereHas('client', function (\Illuminate\Contracts\Database\Query\Builder $clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        $projects = $query->paginate(10)->withQueryString();

        redirect()->redirectIfLastPageEmpty($request, $projects);

        return view('projects.index', ['projects' => $projects]);
    }

    public function create()
    {
        $clients = Client::all();

        return view('projects.create', ['clients' => $clients]);
    }

    public function edit(Project $project)
    {
        $project->load('client');
        $clients = Client::all();

        return view('projects.edit', ['project' => $project, 'clients' => $clients]);
    }

    public function destroy(Project $project)
    {
        $projectName = $project->name;
        $project->delete();

        InAppNotification::success(__('Project :name successfully deleted.', ['name' => $projectName]));

        return to_intended_route('projects.index');
    }
}
