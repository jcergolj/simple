<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\Settings\ProfileController::class)]
class ProfileUpdateTest extends TestCase
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
        $user = User::factory()->create();

        $response = $this->put(route('settings.profile.update'), [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('settings.profile.edit'));

        $response->assertOk();
    }

    #[Test]
    public function profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->from(route('settings.profile.edit'))->put(route('settings.profile.update'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ])->assertValid();

        $user->refresh();

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    #[Test]
    public function email_verification_status_is_unchanged_when_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->from(route('settings.profile.edit'))->put(route('settings.profile.update'), [
            'name' => 'Test User',
            'email' => $user->email,
        ])->assertValid();

        $this->assertNotNull($user->refresh()->email_verified_at);
    }
}
