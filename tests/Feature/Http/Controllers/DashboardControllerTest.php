<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\DashboardController::class)]
class DashboardControllerTest extends TestCase
{
    #[Test]
    public function auth_middleware_is_applied(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function user_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Time Tracking Dashboard')
            ->assertSee('Recent Time Entries');
    }

    #[Test]
    public function dashboard_shows_running_timer(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Joe Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Simple']);

        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => Carbon::now()->subHour(),
            'end_time' => null,
            'duration' => null,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Joe Doe')
            ->assertSee('Simple');
    }

    #[Test]
    public function dashboard_preselects_client_and_project_from_query_params(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Jane Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Jcergolj']);

        $response = $this->actingAs($user)->get(route('dashboard', [
            'client_id' => $client->id,
            'client_name' => $client->name,
            'project_id' => $project->id,
            'project_name' => $project->name,
        ]));

        $response->assertOk()
            ->assertSee('Jane Doe')
            ->assertSee('Jcergolj');
    }

    #[Test]
    public function dashboard_falls_back_to_last_entry_for_preselection(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Jack Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Simple']);

        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => Carbon::now()->subMinutes(30),
            'end_time' => Carbon::now(),
            'duration' => 1800,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Jack Doe')
            ->assertSee('Simple');
    }
}
