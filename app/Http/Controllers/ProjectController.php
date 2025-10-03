<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\ValueObjects\Money;
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

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'client_id' => ['required', 'exists:clients,id'],
                'hourly_rate_amount' => ['nullable', 'numeric', 'min:0'],
                'hourly_rate_currency' => 'required_with:hourly_rate_amount|string|in:'.implode(',', array_column(\App\Enums\Currency::cases(), 'value')),
            ], [
                'name.required' => 'Project name is required.',
                'client_id.required' => 'Client is required.',
                'client_id.exists' => 'Selected client does not exist.',
                'hourly_rate_amount.numeric' => 'Hourly rate must be a valid number.',
                'hourly_rate_amount.min' => 'Hourly rate must be at least 0.',
                'hourly_rate_currency.required_with' => 'Currency is required when hourly rate is specified.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception->redirectTo(route('turbo.projects.create'));
        }

        $hourlyRate = null;
        if (! empty($validated['hourly_rate_amount'])) {
            $hourlyRate = Money::fromDecimal(
                amount: (float) $validated['hourly_rate_amount'],
                currency: $validated['hourly_rate_currency'] ?? 'USD'
            );
        }

        $project = Project::create([
            'name' => $validated['name'],
            'client_id' => $validated['client_id'],
            'hourly_rate' => $hourlyRate,
        ]);

        InAppNotification::success(__('New project successfully created.'));

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return new \Illuminate\Http\JsonResponse([
                'success' => true,
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'client_name' => $project->client->name,
                    'hourly_rate' => $project->hourly_rate ? $project->hourly_rate->formatted() : null,
                ],
                'message' => __('New project successfully created.'),
            ]);
        }

        // Fetch updated list with filters applied
        $query = Project::with('client')->withCount('timeEntries');

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

        return response()
            ->view('turbo::projects.store', [
                'projects' => $projects,
            ])
            ->header('Content-Type', 'text/vnd.turbo-stream.html');
    }

    public function edit(Project $project)
    {
        $project->load('client');
        $clients = Client::all();

        return view('projects.edit', ['project' => $project, 'clients' => $clients]);
    }

    public function update(Request $request, Project $project)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'client_id' => ['required', 'exists:clients,id'],
                'hourly_rate_amount' => ['nullable', 'numeric', 'min:0'],
                'hourly_rate_currency' => 'required_with:hourly_rate_amount|string|in:'.implode(',', array_column(\App\Enums\Currency::cases(), 'value')),
            ], [
                'name.required' => 'Project name is required.',
                'client_id.required' => 'Client is required.',
                'client_id.exists' => 'Selected client does not exist.',
                'hourly_rate_amount.numeric' => 'Hourly rate must be a valid number.',
                'hourly_rate_amount.min' => 'Hourly rate must be at least 0.',
                'hourly_rate_currency.required_with' => 'Currency is required when hourly rate is specified.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception->redirectTo(route('turbo.projects.edit', $project));
        }

        $hourlyRate = null;
        if (! empty($validated['hourly_rate_amount'])) {
            $hourlyRate = Money::fromDecimal(
                amount: (float) $validated['hourly_rate_amount'],
                currency: $validated['hourly_rate_currency'] ?? 'USD'
            );
        }

        $project->update([
            'name' => $validated['name'],
            'client_id' => $validated['client_id'],
            'hourly_rate' => $hourlyRate,
        ]);

        InAppNotification::success(__('Project successfully updated.'));

        // Fetch updated list with filters applied
        $query = Project::with('client')->withCount('timeEntries');

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

        return response()
            ->view('turbo::projects.update', [
                'project' => $project->fresh(['client'])->loadCount('timeEntries'),
                'projects' => $projects,
            ])
            ->header('Content-Type', 'text/vnd.turbo-stream.html');
    }

    public function destroy(Project $project)
    {
        $projectName = $project->name;
        $project->delete();

        InAppNotification::success(__('Project :name successfully deleted.', ['name' => $projectName]));

        return to_intended_route('projects.index');
    }
}
