<?php

namespace Tests\Feature\Http\Controllers\Turbo;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\Turbo\TimeEntryController::class)]
class TimeEntryControllerTest extends TestCase
{
    #[Test]
    public function auth_middleware_is_applied_for_create(): void
    {
        $response = $this->get(route('turbo.time-entries.create'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function auth_middleware_is_applied_for_store(): void
    {
        $response = $this->post(route('turbo.time-entries.store'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function auth_middleware_is_applied_for_edit(): void
    {
        $timeEntry = TimeEntry::factory()->create();

        $response = $this->get(route('turbo.time-entries.edit', $timeEntry));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function auth_middleware_is_applied_for_update(): void
    {
        $timeEntry = TimeEntry::factory()->create();

        $response = $this->patch(route('turbo.time-entries.update', $timeEntry));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function user_can_view_create_form(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Joe Doe']);
        Project::factory()->for($client)->create(['name' => 'Simple']);

        $response = $this->actingAs($user)->get(route('turbo.time-entries.create'));

        $response->assertOk()
            ->assertSee('Start Time')
            ->assertSee('End Time');
    }

    #[Test]
    public function user_can_create_time_entry(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Jane Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Jcergolj']);

        $response = $this->actingAs($user)->post(route('turbo.time-entries.store'), [
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => now()->format('Y-m-d H:i:s'),
            'end_time' => now()->addHour()->format('Y-m-d H:i:s'),
            'notes' => 'Test work',
            'hourly_rate_amount' => '',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('time_entries', [
            'client_id' => $client->id,
            'project_id' => $project->id,
            'notes' => 'Test work',
        ]);
    }

    #[Test]
    public function user_can_view_edit_form(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Jack Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Simple']);
        $timeEntry = TimeEntry::factory()->for($client)->for($project)->create([
            'notes' => 'Original work',
        ]);

        $response = $this->actingAs($user)->get(route('turbo.time-entries.edit', $timeEntry));

        $response->assertOk()
            ->assertSee('Jack Doe')
            ->assertSee('Simple')
            ->assertSee('Original work');
    }

    #[Test]
    public function user_can_update_time_entry(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();
        $project = Project::factory()->for($client)->create();
        $timeEntry = TimeEntry::factory()->for($client)->for($project)->create([
            'notes' => 'Original work',
        ]);

        $response = $this->actingAs($user)->patch(route('turbo.time-entries.update', $timeEntry), [
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => $timeEntry->start_time->format('Y-m-d H:i:s'),
            'end_time' => $timeEntry->end_time ? $timeEntry->end_time->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
            'notes' => 'Updated work',
            'hourly_rate_amount' => '',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('time_entries', [
            'id' => $timeEntry->id,
            'notes' => 'Updated work',
        ]);
    }

    #[Test]
    public function time_entry_creation_requires_end_time(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();
        $project = Project::factory()->for($client)->create();

        $response = $this->actingAs($user)->post(route('turbo.time-entries.store'), [
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => now()->format('Y-m-d H:i:s'),
            // Missing end_time
            'hourly_rate_amount' => '',
            'notes' => '',
        ]);

        $response->assertSessionHasErrors('end_time');
    }

    #[Test]
    public function time_entry_end_time_must_be_after_start_time(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();
        $project = Project::factory()->for($client)->create();

        $startTime = now();
        $endTime = $startTime->copy()->subHour();

        $response = $this->actingAs($user)->post(route('turbo.time-entries.store'), [
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => $startTime->format('Y-m-d H:i:s'),
            'end_time' => $endTime->format('Y-m-d H:i:s'),
            'hourly_rate_amount' => '',
            'notes' => '',
        ]);

        $response->assertSessionHasErrors('end_time');
    }
}
