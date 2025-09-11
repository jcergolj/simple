<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportDateRangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_date_range_selection_is_remembered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('reports.index', ['date_range' => 'this_week']));

        $response->assertOk();
        $response->assertSee('selected', false);
    }

    public function test_this_week_range_filters_correctly(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);

        // Create time entry from this week (use start of week to ensure it's within range)
        $startThisWeek = Carbon::now()->startOfWeek()->addHours(2);
        $endThisWeek = Carbon::now()->startOfWeek()->addHours(3);
        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => $startThisWeek,
            'end_time' => $endThisWeek,
            'duration' => $endThisWeek->diffInSeconds($startThisWeek),
        ]);

        // Create time entry from last month
        $startLastMonth = Carbon::now()->subMonth();
        $endLastMonth = Carbon::now()->subMonth()->addHour();
        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => $startLastMonth,
            'end_time' => $endLastMonth,
            'duration' => $endLastMonth->diffInSeconds($startLastMonth),
        ]);

        $response = $this->actingAs($user)->get(route('reports.index', ['date_range' => 'this_week']));

        $response->assertOk();
        $response->assertSee('1 entry');
    }

    public function test_last_month_range_filters_correctly(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);

        // Create time entry from last month
        $startLastMonth = Carbon::now()->subMonth()->startOfMonth()->addDay();
        $endLastMonth = Carbon::now()->subMonth()->startOfMonth()->addDay()->addHour();
        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => $startLastMonth,
            'end_time' => $endLastMonth,
            'duration' => $endLastMonth->diffInSeconds($startLastMonth),
        ]);

        // Create time entry from this month
        $startThisMonth = Carbon::now();
        $endThisMonth = Carbon::now()->addHour();
        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => $startThisMonth,
            'end_time' => $endThisMonth,
            'duration' => $endThisMonth->diffInSeconds($startThisMonth),
        ]);

        $response = $this->actingAs($user)->get(route('reports.index', ['date_range' => 'last_month']));

        $response->assertOk();
        $response->assertSee('1 entry');
    }

    public function test_export_respects_date_range(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);

        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->startOfWeek()->addHour();
        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => $start,
            'end_time' => $end,
            'duration' => $end->diffInSeconds($start),
        ]);

        $response = $this->actingAs($user)->get(route('report-exports.show', ['date_range' => 'this_week']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
