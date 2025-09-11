<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/dashboard');
        $response->assertOk();
    }

    public function test_start_buttons_are_disabled_when_timer_is_running(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();
        $project = Project::factory()->for($client)->create();

        // Create a completed time entry
        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => now()->subHours(2),
            'end_time' => now()->subHours(1),
            'duration' => 3600,
        ]);

        // Create a running timer
        TimeEntry::factory()->create([
            'start_time' => now()->subMinutes(30),
            'end_time' => null,
            'client_id' => $client->id,
            'project_id' => $project->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Another timer is running');
        $response->assertSee('disabled');
        $response->assertSee('cursor-not-allowed');
    }

    public function test_start_buttons_are_enabled_when_no_timer_is_running(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();
        $project = Project::factory()->for($client)->create();

        // Create only completed time entries
        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => now()->subHours(2),
            'end_time' => now()->subHours(1),
            'duration' => 3600,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertDontSee('Another timer is running');
        $response->assertDontSee('cursor-not-allowed');
        $response->assertSee('bg-green-100');
    }
}
