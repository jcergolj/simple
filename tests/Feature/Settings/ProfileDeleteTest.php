<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->from(route('settings.profile.delete'))->post(route('settings.profile.destroy'), [
            'password' => 'password',
        ])->assertValid()->assertRedirect('/');

        $this->assertGuest();
        $this->assertModelMissing($user);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->from(route('settings.profile.delete'))->post(route('settings.profile.destroy'), [
            'password' => 'wrong-password',
        ])->assertInvalid(['password']);

        $this->assertModelExists($user);
    }
}
