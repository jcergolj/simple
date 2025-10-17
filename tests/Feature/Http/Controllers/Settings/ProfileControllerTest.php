<?php

namespace Tests\Feature\Http\Controllers\Settings;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\Settings\ProfileController::class)]
class ProfileControllerTest extends TestCase
{
    #[Test]
    public function auth_middleware_is_applied_for_edit(): void
    {
        $response = $this->get(route('settings.profile.edit'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function auth_middleware_is_applied_for_update(): void
    {
        $response = $this->patch(route('settings.profile.update'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function user_can_view_profile_edit_form(): void
    {
        $user = User::factory()->create([
            'name' => 'Joe Doe',
            'email' => 'joe.doe@example.com',
        ]);

        $response = $this->actingAs($user)->get(route('settings.profile.edit'));

        $response->assertOk()
            ->assertSee('Edit Profile')
            ->assertSee('Joe Doe')
            ->assertSee('joe.doe@example.com');
    }

    #[Test]
    public function user_can_update_profile_information(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
        ]);

        $response = $this->actingAs($user)->patch(route('settings.profile.update'), [
            'name' => 'Jane Updated Doe',
            'email' => 'jane.updated@example.com',
        ]);

        $response->assertRedirect(route('settings.profile.edit'));

        $user->refresh();
        $this->assertSame('Jane Updated Doe', $user->name);
        $this->assertSame('jane.updated@example.com', $user->email);
    }

    #[Test]
    public function user_can_view_profile_delete_page(): void
    {
        $user = User::factory()->create(['name' => 'Jack Doe']);

        $response = $this->actingAs($user)->get(route('settings.profile.delete'));

        $response->assertOk()
            ->assertSee('Delete Account')
            ->assertSee('Jack Doe');
    }

    #[Test]
    public function user_can_delete_account(): void
    {
        $user = User::factory()->create([
            'name' => 'To Delete',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->post(route('settings.profile.destroy'), [
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    #[Test]
    public function profile_update_requires_valid_email(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('settings.profile.update'), [
            'name' => 'Valid Name',
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function profile_delete_requires_correct_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->actingAs($user)->post(route('settings.profile.destroy'), [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
