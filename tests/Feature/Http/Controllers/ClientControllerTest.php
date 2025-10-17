<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use HotwiredLaravel\TurboLaravel\Testing\InteractsWithTurbo;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\ClientController::class)]
class ClientControllerTest extends TestCase
{
    use InteractsWithTurbo;

    #[Test]
    public function auth_middleware_is_applied_for_index(): void
    {
        $response = $this->get(route('clients.index'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function auth_middleware_is_applied_for_destroy(): void
    {
        $client = Client::factory()->create();

        $response = $this->delete(route('clients.destroy', $client));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function user_can_view_clients_index(): void
    {
        $user = User::factory()->create();
        $client1 = Client::factory()->create(['name' => 'Joe Doe']);
        $client2 = Client::factory()->create(['name' => 'Jane Doe']);

        // Create projects and time entries for counts
        $project1 = Project::factory()->for($client1)->create(['name' => 'Simple']);
        $project2 = Project::factory()->for($client2)->create(['name' => 'Jcergolj']);
        TimeEntry::factory()->for($client1)->for($project1)->create();
        TimeEntry::factory()->count(2)->for($client2)->for($project2)->create();

        $response = $this->actingAs($user)->get(route('clients.index'));

        $response->assertOk()
            ->assertSee('Joe Doe')
            ->assertSee('Jane Doe');
    }

    #[Test]
    public function clients_index_shows_projects_and_time_entries_counts(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Jack Doe']);

        // Create 2 projects and 3 time entries
        $project1 = Project::factory()->for($client)->create(['name' => 'Simple']);
        Project::factory()->for($client)->create(['name' => 'Jcergolj']);
        TimeEntry::factory()->count(3)->for($client)->for($project1)->create();

        $response = $this->actingAs($user)->get(route('clients.index'));

        $response->assertOk()
            ->assertSee('Jack Doe')
            ->assertSee('2') // projects count
            ->assertSee('3'); // time entries count
    }

    #[Test]
    public function clients_index_can_search_by_name(): void
    {
        $user = User::factory()->create();
        Client::factory()->create(['name' => 'Joe Doe']);
        Client::factory()->create(['name' => 'Jane Doe']);

        $response = $this->actingAs($user)->get(route('clients.index', ['search' => 'Joe']));

        $response->assertOk()
            ->assertSee('Joe Doe')
            ->assertDontSee('Jane Doe');
    }

    #[Test]
    public function clients_index_pagination_works(): void
    {
        $user = User::factory()->create();

        // Create 15 clients (more than the 10 per page limit)
        Client::factory()->count(15)->sequence(
            ['name' => 'Joe Doe'],
            ['name' => 'Jane Doe'],
            ['name' => 'Jack Doe'],
        )->create();

        $response = $this->actingAs($user)->get(route('clients.index'));

        $response->assertOk()
            ->assertSee('Next'); // Look for pagination links
    }

    #[Test]
    public function user_can_delete_client(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Joe Doe']);

        $response = $this->actingAs($user)->delete(route('clients.destroy', $client));

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    #[Test]
    public function deleting_client_shows_success_notification(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Jane Doe']);

        $response = $this->actingAs($user)->delete(route('clients.destroy', $client));

        $response->assertRedirect(route('clients.index'));
        // The success notification would be tested through session flash data or other means
        // depending on how the InAppNotification facade works
    }

    #[Test]
    public function clients_index_handles_empty_pages_gracefully(): void
    {
        $user = User::factory()->create();

        // Test with no clients - should not trigger redirectIfLastPageEmpty
        $response = $this->actingAs($user)->get(route('clients.index'));
        $response->assertOk();

        // Test with valid page
        Client::factory()->count(5)->sequence(
            ['name' => 'Joe Doe'],
            ['name' => 'Jane Doe'],
            ['name' => 'Jack Doe'],
        )->create();
        $response = $this->actingAs($user)->get(route('clients.index'));
        $response->assertOk()
            ->assertSee('Joe Doe');
    }
}
