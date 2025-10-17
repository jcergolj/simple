<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\ProjectFilterController::class)]
class ProjectFilterControllerTest extends TestCase
{
    #[Test]
    public function auth_middleware_is_applied(): void
    {
        $response = $this->get(route('project-filter'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function user_can_get_project_filter_data(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Joe Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Simple']);

        $response = $this->actingAs($user)->getJson(route('project-filter', ['client_id' => $client->id]));

        $response->assertOk()
            ->assertJson([
                'projects' => [
                    [
                        'id' => $project->id,
                        'name' => 'Simple',
                        'client_name' => 'Joe Doe',
                    ],
                ],
            ]);
    }

    #[Test]
    public function project_filter_includes_client_relationships(): void
    {
        $user = User::factory()->create();
        $client1 = Client::factory()->create(['name' => 'Jane Doe']);
        $client2 = Client::factory()->create(['name' => 'Jack Doe']);
        Project::factory()->for($client1)->create(['name' => 'Jcergolj']);
        Project::factory()->for($client2)->create(['name' => 'Simple']);

        $response1 = $this->actingAs($user)->getJson(route('project-filter', ['client_id' => $client1->id]));
        $response1->assertOk()
            ->assertJsonFragment(['client_name' => 'Jane Doe'])
            ->assertJsonFragment(['name' => 'Jcergolj']);

        $response2 = $this->actingAs($user)->getJson(route('project-filter', ['client_id' => $client2->id]));
        $response2->assertOk()
            ->assertJsonFragment(['client_name' => 'Jack Doe'])
            ->assertJsonFragment(['name' => 'Simple']);
    }
}
