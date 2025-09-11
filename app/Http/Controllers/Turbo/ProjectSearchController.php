<?php

namespace App\Http\Controllers\Turbo;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\ValueObjects\Money;
use Illuminate\Http\Request;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class ProjectSearchController extends Controller
{
    public function index(Request $request)
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

    public function store(Request $request)
    {
        $request->validate([
            'project_name' => ['required', 'string', 'max:255'],
            'project_client_id' => ['required', 'exists:clients,id'],
            'project_hourly_rate_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'project_hourly_rate_currency' => ['nullable', 'string', 'in:USD,EUR,GBP,JPY,CAD,AUD,CHF,CNY'],
        ], [
            'project_name.required' => 'Project name is required.',
            'project_client_id.required' => 'Client is required.',
            'project_client_id.exists' => 'Selected client does not exist.',
            'project_hourly_rate_amount.numeric' => 'Hourly rate must be a valid number.',
            'project_hourly_rate_amount.min' => 'Hourly rate cannot be negative.',
            'project_hourly_rate_amount.max' => 'Hourly rate cannot exceed 9,999,999.99.',
            'project_hourly_rate_currency.in' => 'Invalid currency selected.',
        ]);

        $hourlyRate = null;
        if ($request->filled('project_hourly_rate_amount')) {
            $hourlyRate = new Money(
                amount: (float) $request->input('project_hourly_rate_amount'),
                currency: $request->input('project_hourly_rate_currency', 'USD')
            );
        }

        $project = Project::create([
            'name' => $request->input('project_name'),
            'client_id' => $request->input('project_client_id'),
            'hourly_rate' => $hourlyRate,
        ]);

        InAppNotification::success('Project "'.$project->name.'" created successfully!');

        return to_route('dashboard', [
            'client_id' => $project->client_id,
            'client_name' => $project->client->name,
            'project_id' => $project->id,
            'project_name' => $project->name,
        ]);
    }
}
