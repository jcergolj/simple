<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use App\View\Components\UserDate;
use App\View\Components\UserDatetime;
use App\View\Components\UserTime;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\Settings\PreferencesController::class)]
class PreferencesTest extends TestCase
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
        User::factory()->create();

        $response = $this->put(route('settings.preferences.update'), [
            'date_format' => 'us',
            'time_format' => '12',
        ]);

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function user_can_view_preferences_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('settings.preferences.edit'));

        $response->assertOk();
        $response->assertSee('Preferences');
        $response->assertSee('Date Format');
    }

    public function test_user_can_update_date_and_time_format_preferences(): void
    {
        $user = User::factory()->create(['date_format' => 'us', 'time_format' => '12']);

        $response = $this->actingAs($user)->put(route('settings.preferences.update'), [
            'date_format' => 'eu',
            'time_format' => '24',
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals('eu', $user->date_format);
        $this->assertEquals('24', $user->time_format);
    }

    public function test_invalid_date_and_time_formats_are_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('settings.preferences.update'), [
            'date_format' => 'invalid',
            'time_format' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['date_format', 'time_format']);
    }

    public function test_user_date_component_formats_correctly(): void
    {
        $testDate = Carbon::create(2025, 12, 25, 14, 30, 0);

        // Test US format
        $user = User::factory()->create(['date_format' => 'us']);
        $this->actingAs($user);
        $component = new UserDate($testDate);
        $this->assertSame('12/25/2025', $component->formattedDate);

        // Test UK format
        $user->update(['date_format' => 'uk']);
        $this->actingAs($user);
        $component = new UserDate($testDate);
        $this->assertSame('25/12/2025', $component->formattedDate);

        // Test EU format
        $user->update(['date_format' => 'eu']);
        $this->actingAs($user);
        $component = new UserDate($testDate);
        $this->assertSame('25.12.2025', $component->formattedDate);
    }

    public function test_user_datetime_component_formats_correctly(): void
    {
        $testDate = Carbon::create(2025, 12, 25, 14, 30, 0);

        // Test US + 12h
        $user = User::factory()->create(['date_format' => 'us', 'time_format' => '12']);
        $this->actingAs($user);
        $component = new UserDatetime($testDate);
        $this->assertSame('12/25/2025 2:30 PM', $component->formattedDatetime);

        // Test UK + 24h
        $user->update(['date_format' => 'uk', 'time_format' => '24']);
        $this->actingAs($user);
        $component = new UserDatetime($testDate);
        $this->assertSame('25/12/2025 14:30', $component->formattedDatetime);

        // Test EU + 12h (mixed format)
        $user->update(['date_format' => 'eu', 'time_format' => '12']);
        $this->actingAs($user);
        $component = new UserDatetime($testDate);
        $this->assertSame('25.12.2025 2:30 PM', $component->formattedDatetime);
    }

    public function test_user_time_component_formats_correctly(): void
    {
        $testDate = Carbon::create(2025, 12, 25, 14, 30, 0);

        // Test 12-hour format
        $user = User::factory()->create(['time_format' => '12']);
        $this->actingAs($user);
        $component = new UserTime($testDate);
        $this->assertSame('2:30 PM', $component->formattedTime);

        // Test 24-hour format
        $user->update(['time_format' => '24']);
        $this->actingAs($user);
        $component = new UserTime($testDate);
        $this->assertSame('14:30', $component->formattedTime);
    }

    public function test_components_handle_null_date_gracefully(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $dateComponent = new UserDate(null);
        $datetimeComponent = new UserDatetime(null);

        $this->assertSame('', $dateComponent->formattedDate);
        $this->assertSame('', $datetimeComponent->formattedDatetime);
    }

    public function test_components_work_without_authenticated_user(): void
    {
        $testDate = Carbon::create(2025, 12, 25, 14, 30, 0);

        $dateComponent = new UserDate($testDate);
        $datetimeComponent = new UserDatetime($testDate);

        // Should fall back to default format
        $this->assertSame('Dec 25, 2025', $dateComponent->formattedDate);
        $this->assertSame('Dec 25, 2025 2:30 PM', $datetimeComponent->formattedDatetime);
    }
}
