<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\DashboardController::class)]
class DashboardTest extends TestCase
{
    #[Test]
    public function auth_middleware_is_applied(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
    }

    #[Test]
    public function start_buttons_are_disabled_when_timer_is_running(): void
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

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Another timer is running')
            ->assertSee('disabled')
            ->assertSee('cursor-not-allowed');
    }

    #[Test]
    public function start_buttons_are_enabled_when_no_timer_is_running(): void
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

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertDontSee('Another timer is running')
            ->assertDontSee('cursor-not-allowed')
            ->assertSee('bg-green-100');
    }
}
