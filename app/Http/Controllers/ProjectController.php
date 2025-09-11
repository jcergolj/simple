<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveProjectRequest;
use App\Models\Client;
use App\Models\Project;
use App\ValueObjects\Money;
use Illuminate\Http\Request;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::with('client')->withCount('timeEntries')->paginate(10);

        redirect()->redirectIfLastPageEmpty($request, $projects);

        return view('projects.index', ['projects' => $projects]);
    }

    public function create()
    {
        $clients = Client::all();

        return view('projects.create', ['clients' => $clients]);
    }

    public function store(SaveProjectRequest $request)
    {
        $validated = $request->validated();

        $hourlyRate = null;
        if ($validated['hourly_rate_amount']) {
            $hourlyRate = new Money(
                amount: (float) $validated['hourly_rate_amount'],
                currency: $validated['hourly_rate_currency'] ?? 'USD'
            );
        }

        Project::create([
            'name' => $validated['name'],
            'client_id' => $validated['client_id'],
            'description' => $validated['description'],
            'hourly_rate' => $hourlyRate,
        ]);

        InAppNotification::success(__('New project successfully created.'));

        return to_intended_route('projects.index');
    }

    public function edit(Project $project)
    {
        $project->load('client');
        $clients = Client::all();

        return view('projects.edit', ['project' => $project, 'clients' => $clients]);
    }

    public function update(SaveProjectRequest $request, Project $project)
    {
        $validated = $request->validated();

        $hourlyRate = null;
        if ($validated['hourly_rate_amount']) {
            $hourlyRate = new Money(
                amount: (float) $validated['hourly_rate_amount'],
                currency: $validated['hourly_rate_currency'] ?? 'USD'
            );
        }

        $project->update([
            'name' => $validated['name'],
            'client_id' => $validated['client_id'],
            'description' => $validated['description'],
            'hourly_rate' => $hourlyRate,
        ]);

        InAppNotification::success(__('Project successfully updated.'));

        return to_intended_route('projects.index');
    }

    public function destroy(Project $project)
    {
        $projectName = $project->name;
        $project->delete();

        InAppNotification::success(__('Project :name successfully deleted.', ['name' => $projectName]));

        return to_intended_route('projects.index');
    }
}
