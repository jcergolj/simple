<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectFilterTest extends TestCase
{
    public $user;

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_project_filter_returns_correct_frame_id_for_desktop(): void
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);

        $response = $this->withHeaders([
            'Turbo-Frame' => 'project-filter-desktop',
        ])->get(route('project-filter', [
            'client_id' => $client->id,
            'selected_project_id' => $project->id,
        ]));

        $response->assertOk();
        $response->assertSee('id="project-filter-desktop"', false);
        $response->assertSee($project->name);
    }

    public function test_project_filter_returns_correct_frame_id_for_mobile(): void
    {
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);

        $response = $this->withHeaders([
            'Turbo-Frame' => 'project-filter-mobile',
        ])->get(route('project-filter', [
            'client_id' => $client->id,
            'selected_project_id' => $project->id,
        ]));

        $response->assertOk();
        $response->assertSee('id="project-filter-mobile"', false);
        $response->assertSee($project->name);
    }

    public function test_project_filter_defaults_to_desktop_when_no_frame_header(): void
    {
        $client = Client::factory()->create();

        $response = $this->get(route('project-filter', [
            'client_id' => $client->id,
        ]));

        $response->assertOk();
        $response->assertSee('id="project-filter-desktop"', false);
    }

    public function test_project_filter_shows_empty_projects_when_no_client_provided(): void
    {
        $response = $this->withHeaders([
            'Turbo-Frame' => 'project-filter-mobile',
        ])->get(route('project-filter'));

        $response->assertOk();
        $response->assertSee('id="project-filter-mobile"', false);
        $response->assertSee(__('Select a client first'));
    }
}
