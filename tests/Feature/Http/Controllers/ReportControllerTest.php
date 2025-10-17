<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\ReportController::class)]
class ReportControllerTest extends TestCase
{
    #[Test]
    public function auth_middleware_is_applied(): void
    {
        $response = $this->get(route('reports.index'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function user_can_view_reports_index(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Joe Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Simple']);

        // Create time entries for reports
        TimeEntry::factory()->for($client)->for($project)->create([
            'start_time' => Carbon::now()->subDays(5),
            'end_time' => Carbon::now()->subDays(5)->addHours(2),
            'duration' => 7200,
        ]);

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertOk()
            ->assertSee('Reports');
    }

    #[Test]
    public function reports_can_be_filtered_by_date_range(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Jane Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Jcergolj']);

        TimeEntry::factory()->for($client)->for($project)->create([
            'start_time' => Carbon::now()->subDays(10),
            'end_time' => Carbon::now()->subDays(10)->addHours(3),
            'duration' => 10800,
        ]);

        $response = $this->actingAs($user)->get(route('reports.index', [
            'date_from' => Carbon::now()->subDays(15)->format('Y-m-d'),
            'date_to' => Carbon::now()->subDays(5)->format('Y-m-d'),
        ]));

        $response->assertOk()
            ->assertSee('Jane Doe')
            ->assertSee('Jcergolj');
    }

    #[Test]
    public function reports_can_be_filtered_by_client(): void
    {
        $user = User::factory()->create();
        $client1 = Client::factory()->create(['name' => 'Jack Doe']);
        $client2 = Client::factory()->create(['name' => 'Joe Doe']);
        $project1 = Project::factory()->for($client1)->create(['name' => 'Simple']);
        $project2 = Project::factory()->for($client2)->create(['name' => 'Jcergolj']);

        TimeEntry::factory()->for($client1)->for($project1)->create();
        TimeEntry::factory()->for($client2)->for($project2)->create();

        $response = $this->actingAs($user)->get(route('reports.index', [
            'client_id' => $client1->id,
        ]));

        $response->assertOk()
            ->assertSee('Simple')
            ->assertDontSee('Jcergolj');
    }

    #[Test]
    public function reports_can_be_filtered_by_project(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Jane Doe']);
        $project1 = Project::factory()->for($client)->create(['name' => 'Simple']);
        $project2 = Project::factory()->for($client)->create(['name' => 'Jcergolj']);

        TimeEntry::factory()->for($client)->for($project1)->create();
        TimeEntry::factory()->for($client)->for($project2)->create();

        $response = $this->actingAs($user)->get(route('reports.index', [
            'project_id' => $project1->id,
        ]));

        $response->assertOk()
            ->assertSee('Simple')
            ->assertDontSee('Jcergolj');
    }
}
