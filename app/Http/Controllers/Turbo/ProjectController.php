<?php

namespace App\Http\Controllers\Turbo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Turbo\StoreProjectRequest;
use App\Http\Requests\Turbo\UpdateProjectRequest;
use App\Models\Project;
use App\ValueObjects\Money;

class ProjectController extends Controller
{
    public function create()
    {
        return view('turbo::projects.create');
    }

    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();

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
        return view('turbo::projects.edit', ['project' => $project]);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $validated = $request->validated();

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
}
