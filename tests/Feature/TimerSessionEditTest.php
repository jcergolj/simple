<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimerSessionEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_edit_form_for_running_timer(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();
        $project = Project::factory()->for($client)->create();

        TimeEntry::factory()->create([
            'start_time' => now()->subMinutes(30),
            'end_time' => null,
            'client_id' => $client->id,
            'project_id' => $project->id,
        ]);

        $response = $this->actingAs($user)->get(route('turbo.running-timer-session.edit'));

        $response->assertOk();
        $response->assertSee('Edit Running Session');
        $response->assertSee($client->name);
        $response->assertSee($project->name);
    }

    public function test_user_can_update_running_timer_details(): void
    {
        $user = User::factory()->create();
        $oldClient = Client::factory()->create(['name' => 'Old Client']);
        $newClient = Client::factory()->create(['name' => 'New Client']);
        $newProject = Project::factory()->for($newClient)->create(['name' => 'New Project']);

        $runningTimer = TimeEntry::factory()->create([
            'start_time' => now()->subHours(1),
            'end_time' => null,
            'client_id' => $oldClient->id,
            'project_id' => null,
        ]);

        $newStartTime = now()->subMinutes(45);

        $response = $this->actingAs($user)->put(route('turbo.running-timer-session.update'), [
            'client_id' => $newClient->id,
            'project_id' => $newProject->id,
            'start_time' => $newStartTime->format('Y-m-d\TH:i'),
        ]);

        $response->assertOk();

        $runningTimer->refresh();
        $this->assertEquals($newClient->id, $runningTimer->client_id);
        $this->assertEquals($newProject->id, $runningTimer->project_id);
        $this->assertEquals($newStartTime->format('Y-m-d H:i'), $runningTimer->start_time->format('Y-m-d H:i'));
    }

    public function test_user_cannot_set_start_time_in_future(): void
    {
        $user = User::factory()->create();
        TimeEntry::factory()->create([
            'start_time' => now()->subMinutes(30),
            'end_time' => null,
        ]);

        $futureTime = now()->addMinutes(30);

        $response = $this->actingAs($user)->put(route('turbo.running-timer-session.update'), [
            'client_id' => null,
            'project_id' => null,
            'start_time' => $futureTime->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('start_time');
    }

    public function test_edit_redirects_when_no_running_timer(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('turbo.running-timer-session.edit'));

        $response->assertOk();
        $response->assertSee('Ready to start'); // Should show start view instead
    }

    public function test_patch_redirects_when_no_running_timer(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('turbo.running-timer-session.update'), [
            'client_id' => null,
            'project_id' => null,
            'start_time' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('turbo.running-timer-session.show'));
    }
}
