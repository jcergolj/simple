<?php

namespace Tests\Feature\Http\Controllers\Turbo;

use App\Models\Client;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\Turbo\ClientController::class)]
class ClientControllerTest extends TestCase
{
    #[Test]
    public function auth_middleware_is_applied_for_create(): void
    {
        $response = $this->get(route('turbo.clients.create'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function auth_middleware_is_applied_for_store(): void
    {
        $response = $this->post(route('turbo.clients.store'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function auth_middleware_is_applied_for_edit(): void
    {
        $client = Client::factory()->create();

        $response = $this->get(route('turbo.clients.edit', $client));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function auth_middleware_is_applied_for_update(): void
    {
        $client = Client::factory()->create();

        $response = $this->patch(route('turbo.clients.update', $client));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function user_can_view_create_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('turbo.clients.create'));

        $response->assertOk()
            ->assertSee('Name')
            ->assertSee('Create');
    }

    #[Test]
    public function user_can_create_client(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('turbo.clients.store'), [
            'name' => 'Joe Doe',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('clients', ['name' => 'Joe Doe']);
    }

    #[Test]
    public function user_can_view_edit_form(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Jane Doe']);

        $response = $this->actingAs($user)->get(route('turbo.clients.edit', $client));

        $response->assertOk()
            ->assertSee('Jane Doe')
            ->assertSee('Update');
    }

    #[Test]
    public function user_can_update_client(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Jack Doe']);

        $response = $this->actingAs($user)->patch(route('turbo.clients.update', $client), [
            'name' => 'Jack Updated Doe',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('clients', ['name' => 'Jack Updated Doe']);
    }

    #[Test]
    public function client_creation_requires_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('turbo.clients.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function client_update_requires_name(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $response = $this->actingAs($user)->patch(route('turbo.clients.update', $client), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
