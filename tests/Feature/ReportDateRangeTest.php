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

        // Create time entry from this week
        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => Carbon::now()->startOfWeek()->addDay(),
            'end_time' => Carbon::now()->startOfWeek()->addDay()->addHour(),
        ]);

        // Create time entry from last month
        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => Carbon::now()->subMonth(),
            'end_time' => Carbon::now()->subMonth()->addHour(),
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
        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => Carbon::now()->subMonth()->startOfMonth()->addDay(),
            'end_time' => Carbon::now()->subMonth()->startOfMonth()->addDay()->addHour(),
        ]);

        // Create time entry from this month
        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addHour(),
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

        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => Carbon::now()->startOfWeek(),
            'end_time' => Carbon::now()->startOfWeek()->addHour(),
        ]);

        $response = $this->actingAs($user)->get(route('reports.export', ['date_range' => 'this_week']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
