<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/confirm-password')
            ->assertOk();
    }

    public function test_password_can_be_confirmed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->from('/confirm-password')->post(route('password.confirm.store'), [
            'password' => 'password',
        ])->assertValid()->assertRedirect(route('dashboard'));
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->from('/confirm-password')->post(route('password.confirm.store'), [
            'password' => 'wrong-password',
        ])->assertInvalid(['password']);
    }
}
