<?php

namespace Tests\Feature\Http\Controllers\Settings;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\Settings\PreferencesController::class)]
class PreferencesControllerTest extends TestCase
{
    #[Test]
    public function auth_middleware_is_applied_for_edit(): void
    {
        $response = $this->get(route('settings.preferences.edit'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function auth_middleware_is_applied_for_update(): void
    {
        $response = $this->patch(route('settings.preferences.update'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function user_can_view_preferences_form(): void
    {
        $user = User::factory()->create(['name' => 'Joe Doe']);

        $response = $this->actingAs($user)->get(route('settings.preferences.edit'));

        $response->assertOk()
            ->assertSee('Preferences')
            ->assertSee('Date Format')
            ->assertSee('Time Format');
    }

    #[Test]
    public function user_can_update_date_format_preference(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'date_format' => 'us',
        ]);

        $response = $this->actingAs($user)->patch(route('settings.preferences.update'), [
            'date_format' => 'uk',
            'time_format' => $user->time_format ?? '24',
        ]);

        $response->assertRedirect(route('settings.preferences.edit'));

        $user->refresh();
        $this->assertSame('uk', $user->date_format);
    }

    #[Test]
    public function user_can_update_time_format_preference(): void
    {
        $user = User::factory()->create([
            'name' => 'Jack Doe',
            'time_format' => '24',
        ]);

        $response = $this->actingAs($user)->patch(route('settings.preferences.update'), [
            'date_format' => $user->date_format ?? 'us',
            'time_format' => '12',
        ]);

        $response->assertRedirect(route('settings.preferences.edit'));

        $user->refresh();
        $this->assertSame('12', $user->time_format);
    }

    #[Test]
    public function preferences_update_requires_valid_date_format(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('settings.preferences.update'), [
            'date_format' => 'invalid-format',
            'time_format' => '24',
        ]);

        $response->assertSessionHasErrors('date_format');
    }

    #[Test]
    public function preferences_update_requires_valid_time_format(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('settings.preferences.update'), [
            'date_format' => 'us',
            'time_format' => 'invalid-format',
        ]);

        $response->assertSessionHasErrors('time_format');
    }
}
