<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')->assertOk();
    }

    public function test_new_users_can_register(): void
    {
        $this->withoutMiddleware()->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertValid()->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_new_users_can_register_with_hourly_rate(): void
    {
        $this->withoutMiddleware()->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'hourly_rate_amount' => '50.00',
            'hourly_rate_currency' => 'USD',
        ])->assertValid()->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();

        $user = \App\Models\User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user->hourly_rate);
        $this->assertEqualsWithDelta(50.0, $user->hourly_rate->amount, PHP_FLOAT_EPSILON);
        $this->assertEquals('USD', $user->hourly_rate->currency);
    }
}
