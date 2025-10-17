<?php

namespace Tests\Feature\Auth;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    #[Test]
    public function guest_middleware_is_applied(): void
    {
        $response = $this->get(route('register'));

        $response->assertMiddlewareIsApplied('guest');
    }

    #[Test]
    public function registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    #[Test]
    public function new_users_can_register(): void
    {
        $response = $this->withoutMiddleware()->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertValid()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }
}
