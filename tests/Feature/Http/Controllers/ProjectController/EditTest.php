<?php

namespace Tests\Feature\Http\Controllers\ProjectController;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\ProjectController::class)]
#[\PHPUnit\Framework\Attributes\CoversMethod(\App\Http\Controllers\ProjectController::class, 'edit')]
class EditTest extends TestCase
{
    #[Test]
    public function assert_auth_middleware_is_applied(): void
    {
        $project = Project::factory()->create();

        $response = $this->get(route('turbo.projects.edit', $project));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function user_can_view_edit_form(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Joe Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Simple']);

        $response = $this->actingAs($user)
            ->get(route('turbo.projects.edit', $project));

        $response->assertOk()
            ->assertSee('Simple')
            ->assertSee('Joe Doe')
            ->assertSee('Update');
    }

    #[Test]
    public function edit_form_loads_project_with_client_relationship(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Jane Doe']);
        $project = Project::factory()
            ->for($client)
            ->create(['name' => 'Jcergolj']);

        $response = $this->actingAs($user)
            ->get(route('turbo.projects.edit', $project));

        $response->assertOk()
            ->assertSee('Jcergolj')
            ->assertSee('Jane Doe');
    }

    #[Test]
    public function edit_form_displays_client_search_component(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        Client::factory()->create(['name' => 'Jack Doe']);
        Client::factory()->create(['name' => 'Joe Doe']);

        $response = $this->actingAs($user)
            ->get(route('turbo.projects.edit', $project));

        $response->assertOk()
            ->assertSee('Search or create client');
    }
}
