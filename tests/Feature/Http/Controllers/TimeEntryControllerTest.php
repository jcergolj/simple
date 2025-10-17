<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\TimeEntryController::class)]
class TimeEntryControllerTest extends TestCase
{
    #[Test]
    public function auth_middleware_is_applied_for_index(): void
    {
        $response = $this->get(route('time-entries.index'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function auth_middleware_is_applied_for_destroy(): void
    {
        $timeEntry = TimeEntry::factory()->create();

        $response = $this->delete(route('time-entries.destroy', $timeEntry));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function user_can_view_time_entries_index(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Joe Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Simple']);

        TimeEntry::factory()->for($client)->for($project)->create([
            'start_time' => Carbon::now()->subHours(2),
            'end_time' => Carbon::now()->subHour(),
            'duration' => 3600,
        ]);

        TimeEntry::factory()->for($client)->for($project)->create([
            'start_time' => Carbon::now()->subHours(4),
            'end_time' => Carbon::now()->subHours(3),
            'duration' => 3600,
        ]);

        $response = $this->actingAs($user)->get(route('time-entries.index'));

        $response->assertOk()
            ->assertSee('Joe Doe')
            ->assertSee('Simple');
    }

    #[Test]
    public function time_entries_can_be_filtered_by_client(): void
    {
        $user = User::factory()->create();
        $client1 = Client::factory()->create(['name' => 'Jane Doe']);
        $client2 = Client::factory()->create(['name' => 'Jack Doe']);
        $project1 = Project::factory()->for($client1)->create(['name' => 'Jcergolj']);
        $project2 = Project::factory()->for($client2)->create(['name' => 'Simple']);

        TimeEntry::factory()->for($client1)->for($project1)->create();
        TimeEntry::factory()->for($client2)->for($project2)->create();

        $response = $this->actingAs($user)->get(route('time-entries.index', [
            'client_id' => $client1->id,
        ]));

        $response->assertOk()
            ->assertSee('Jcergolj')
            ->assertDontSee('Simple');
    }

    #[Test]
    public function time_entries_can_be_filtered_by_project(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Joe Doe']);
        $project1 = Project::factory()->for($client)->create(['name' => 'Simple']);
        $project2 = Project::factory()->for($client)->create(['name' => 'Jcergolj']);

        TimeEntry::factory()->for($client)->for($project1)->create();
        TimeEntry::factory()->for($client)->for($project2)->create();

        $response = $this->actingAs($user)->get(route('time-entries.index', [
            'project_id' => $project1->id,
        ]));

        $response->assertOk()
            ->assertSee('Simple')
            ->assertDontSee('Jcergolj');
    }

    #[Test]
    public function time_entries_can_be_filtered_by_date_range(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Jane Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Simple']);

        TimeEntry::factory()->for($client)->for($project)->create([
            'start_time' => Carbon::now()->subDays(10),
        ]);

        TimeEntry::factory()->for($client)->for($project)->create([
            'start_time' => Carbon::now()->subDays(2),
        ]);

        $response = $this->actingAs($user)->get(route('time-entries.index', [
            'date_from' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'date_to' => Carbon::now()->format('Y-m-d'),
        ]));

        $response->assertOk()
            ->assertSee('Jane Doe')
            ->assertSee('Simple');
    }

    #[Test]
    public function time_entries_are_paginated(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Jack Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Jcergolj']);

        TimeEntry::factory()->count(25)->for($client)->for($project)->create();

        $response = $this->actingAs($user)->get(route('time-entries.index'));

        $response->assertOk()
            ->assertSee('Next'); // Pagination links
    }

    #[Test]
    public function user_can_delete_time_entry(): void
    {
        $user = User::factory()->create();
        $timeEntry = TimeEntry::factory()->create();

        $response = $this->actingAs($user)->delete(route('time-entries.destroy', $timeEntry));

        $response->assertRedirect(route('time-entries.index'));
        $this->assertDatabaseMissing('time_entries', ['id' => $timeEntry->id]);
    }

    #[Test]
    public function deleting_recent_time_entry_redirects_to_dashboard(): void
    {
        $user = User::factory()->create();
        $timeEntry = TimeEntry::factory()->create();

        $response = $this->actingAs($user)->delete(route('time-entries.destroy', $timeEntry), [
            'is_recent' => true,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseMissing('time_entries', ['id' => $timeEntry->id]);
    }

    #[Test]
    public function time_entries_index_handles_empty_pages_gracefully(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Joe Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Simple']);

        // Test with no time entries
        $response = $this->actingAs($user)->get(route('time-entries.index'));
        $response->assertOk();

        // Test with valid page
        TimeEntry::factory()->count(20)->for($client)->for($project)->create();
        $response = $this->actingAs($user)->get(route('time-entries.index'));
        $response->assertOk()
            ->assertSee('Joe Doe');
    }
}
