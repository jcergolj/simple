<?php

namespace Tests\Feature\Http\Controllers\Settings;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\Settings\PasswordController::class)]
class PasswordControllerTest extends TestCase
{
    #[Test]
    public function auth_middleware_is_applied_for_edit(): void
    {
        $response = $this->get(route('settings.password.edit'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function auth_middleware_is_applied_for_update(): void
    {
        $response = $this->patch(route('settings.password.update'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function user_can_view_password_change_form(): void
    {
        $user = User::factory()->create(['name' => 'Joe Doe']);

        $response = $this->actingAs($user)->get(route('settings.password.edit'));

        $response->assertOk()
            ->assertSee('Update Password')
            ->assertSee('Current Password')
            ->assertSee('New Password');
    }

    #[Test]
    public function user_can_update_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'password' => bcrypt('old-password'),
        ]);

        $response = $this->actingAs($user)->patch(route('settings.password.update'), [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('settings.password.edit'));

        $user->refresh();
        $this->assertTrue(\Hash::check('new-password', $user->password));
    }

    #[Test]
    public function password_update_requires_current_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('current-password'),
        ]);

        $response = $this->actingAs($user)->patch(route('settings.password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    #[Test]
    public function password_update_requires_confirmation(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('current-password'),
        ]);

        $response = $this->actingAs($user)->patch(route('settings.password.update'), [
            'current_password' => 'current-password',
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function password_must_meet_minimum_length(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('current-password'),
        ]);

        $response = $this->actingAs($user)->patch(route('settings.password.update'), [
            'current_password' => 'current-password',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
