<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimerSessionUnifiedTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_returns_running_blade_and_stop_uses_turbo_stream(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();
        $project = Project::factory()->for($client)->create();

        TimeEntry::factory()->create([
            'start_time' => now()->subMinutes(30),
            'end_time' => null,
            'client_id' => null,
            'project_id' => null,
        ]);

        // Test edit response returns running blade (not turbo-stream)
        $editResponse = $this->actingAs($user)->put(route('turbo.running-timer-session.update'), [
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => now()->subMinutes(15)->format('Y-m-d\TH:i'),
        ]);

        $editResponse->assertOk();
        $editResponse->assertSee('Session in progress');
        $editResponse->assertSee($client->name);
        $editResponse->assertSee($project->name);

        // Test stop response uses turbo-stream template
        $stopResponse = $this->actingAs($user)->post(route('turbo.running-timer-session.completion'));

        $stopResponse->assertOk();
        $stopResponse->assertHeader('Content-Type', 'text/vnd.turbo-stream.html; charset=UTF-8');

        // Verify the stop response includes all necessary turbo-stream updates
        $stopContent = $stopResponse->getContent();
        $this->assertStringContainsString('action="replace" target="timer-widget"', (string) $stopContent);
        $this->assertStringContainsString('action="replace" target="recent-entries"', (string) $stopContent);
        $this->assertStringContainsString('action="replace" target="weekly-hours"', (string) $stopContent);
        $this->assertStringContainsString('action="replace" target="weekly-earnings"', (string) $stopContent);
    }

    public function test_recent_entries_updated_correctly_after_edit_and_stop(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Updated Client']);
        $project = Project::factory()->for($client)->create(['name' => 'Updated Project']);

        // Create a running timer without client/project
        $runningTimer = TimeEntry::factory()->create([
            'start_time' => now()->subMinutes(30),
            'end_time' => null,
            'client_id' => null,
            'project_id' => null,
        ]);

        // Edit the timer to add client/project
        $this->actingAs($user)->put(route('turbo.running-timer-session.update'), [
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => now()->subMinutes(15)->format('Y-m-d\TH:i'),
        ]);

        // Verify timer was updated
        $runningTimer->refresh();
        $this->assertEquals($client->id, $runningTimer->client_id);
        $this->assertEquals($project->id, $runningTimer->project_id);

        // Stop the timer
        $this->actingAs($user)->post(route('turbo.running-timer-session.completion'));

        // Verify the timer is stopped and has correct data
        $stoppedTimer = TimeEntry::find($runningTimer->id);
        $this->assertNotNull($stoppedTimer->end_time);
        $this->assertEquals($client->id, $stoppedTimer->client_id);
        $this->assertEquals($project->id, $stoppedTimer->project_id);

        // Verify recent entries show the correct updated timer
        $recentEntries = TimeEntry::with(['client', 'project'])
            ->latest('start_time')
            ->limit(5)
            ->get();

        $this->assertTrue($recentEntries->contains('id', $stoppedTimer->id));
        $recentTimer = $recentEntries->firstWhere('id', $stoppedTimer->id);
        $this->assertEquals('Updated Client', $recentTimer->client->name);
        $this->assertEquals('Updated Project', $recentTimer->project->name);
    }

    public function test_cancel_timer_also_uses_unified_template(): void
    {
        $user = User::factory()->create();
        $runningTimer = TimeEntry::factory()->create([
            'start_time' => now()->subMinutes(30),
            'end_time' => null,
        ]);

        $response = $this->actingAs($user)->delete(route('turbo.running-timer-session.destroy'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/vnd.turbo-stream.html; charset=UTF-8');

        // Verify all dashboard sections are updated
        $content = $response->getContent();
        $this->assertStringContainsString('action="replace" target="timer-widget"', (string) $content);
        $this->assertStringContainsString('action="replace" target="recent-entries"', (string) $content);
        $this->assertStringContainsString('action="replace" target="weekly-hours"', (string) $content);
        $this->assertStringContainsString('action="replace" target="weekly-earnings"', (string) $content);

        // Verify timer was deleted
        $this->assertDatabaseMissing('time_entries', ['id' => $runningTimer->id]);
    }

    public function test_running_timer_in_recent_entries_updates_immediately(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Updated Recent Client']);
        $project = Project::factory()->for($client)->create(['name' => 'Updated Recent Project']);

        // Create a running timer that will appear in recent entries
        $runningTimer = TimeEntry::factory()->create([
            'start_time' => now()->subMinutes(5), // Recent enough to be in top 5
            'end_time' => null,
            'client_id' => null,
            'project_id' => null,
        ]);

        // Verify timer appears in recent entries before edit
        $recentBefore = TimeEntry::with(['client', 'project'])
            ->latest('start_time')
            ->limit(5)
            ->get();
        $this->assertTrue($recentBefore->contains('id', $runningTimer->id));

        // Edit the running timer
        $response = $this->actingAs($user)->put(route('turbo.running-timer-session.update'), [
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => now()->subMinutes(3)->format('Y-m-d\TH:i'),
        ]);

        // Verify response is turbo-stream that updates recent entries
        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/vnd.turbo-stream.html; charset=UTF-8');

        $content = $response->getContent();
        $this->assertStringContainsString('action="replace" target="recent-entries"', (string) $content);

        // Verify the running timer was updated in database
        $runningTimer->refresh();
        $this->assertEquals($client->id, $runningTimer->client_id);
        $this->assertEquals($project->id, $runningTimer->project_id);

        // Verify recent entries now show the updated running timer
        $recentAfter = TimeEntry::with(['client', 'project'])
            ->latest('start_time')
            ->limit(5)
            ->get();

        $updatedTimer = $recentAfter->firstWhere('id', $runningTimer->id);
        $this->assertInstanceOf(TimeEntry::class, $updatedTimer, 'Running timer should still be in recent entries');
        $this->assertEquals('Updated Recent Client', $updatedTimer->client->name);
        $this->assertEquals('Updated Recent Project', $updatedTimer->project->name);
        $this->assertNull($updatedTimer->end_time, 'Timer should still be running');
    }
}
