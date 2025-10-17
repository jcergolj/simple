<?php

namespace Tests\Unit\View\Components;

use App\Enums\DateFormat;
use App\Models\User;
use App\View\Components\UserDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\View\Components\UserDate::class)]
class UserDateTest extends TestCase
{
    #[Test]
    public function formats_date_with_user_preference(): void
    {
        $user = User::factory()->make([
            'date_format' => DateFormat::US->value,
        ]);
        Auth::shouldReceive('user')->andReturn($user);

        $date = Carbon::parse('2024-12-25');
        $component = new UserDate($date);

        $this->assertSame('12/25/2024', $component->formattedDate);
    }

    #[Test]
    public function formats_date_with_uk_preference(): void
    {
        $user = User::factory()->make([
            'date_format' => DateFormat::UK->value,
        ]);
        Auth::shouldReceive('user')->andReturn($user);

        $date = Carbon::parse('2024-12-25');
        $component = new UserDate($date);

        $this->assertSame('25/12/2024', $component->formattedDate);
    }

    #[Test]
    public function formats_date_with_eu_preference(): void
    {
        $user = User::factory()->make([
            'date_format' => DateFormat::EU->value,
        ]);
        Auth::shouldReceive('user')->andReturn($user);

        $date = Carbon::parse('2024-12-25');
        $component = new UserDate($date);

        $this->assertSame('25.12.2024', $component->formattedDate);
    }

    #[Test]
    public function uses_fallback_format_when_no_user(): void
    {
        Auth::shouldReceive('user')->andReturn(null);

        $date = Carbon::parse('2024-12-25');
        $component = new UserDate($date);

        $this->assertSame('Dec 25, 2024', $component->formattedDate);
    }

    #[Test]
    public function handles_string_date_input(): void
    {
        $user = User::factory()->make([
            'date_format' => DateFormat::US->value,
        ]);
        Auth::shouldReceive('user')->andReturn($user);

        $component = new UserDate('2024-12-25 10:30:00');

        $this->assertSame('12/25/2024', $component->formattedDate);
    }

    #[Test]
    public function returns_fallback_for_empty_date(): void
    {
        $component = new UserDate(null, 'No date');

        $this->assertSame('No date', $component->formattedDate);
    }

    #[Test]
    public function returns_empty_string_for_null_date_without_fallback(): void
    {
        $component = new UserDate(null);

        $this->assertSame('', $component->formattedDate);
    }

    #[Test]
    public function stores_original_date_property(): void
    {
        $date = Carbon::parse('2024-12-25');
        $component = new UserDate($date);

        $this->assertSame($date, $component->date);
    }

    #[Test]
    public function stores_fallback_property(): void
    {
        $component = new UserDate(null, 'Custom fallback');

        $this->assertSame('Custom fallback', $component->fallback);
    }

    #[Test]
    public function handles_empty_string_date(): void
    {
        $component = new UserDate('', 'Empty date');

        $this->assertSame('Empty date', $component->formattedDate);
    }

    #[Test]
    public function render_returns_view(): void
    {
        $component = new UserDate(Carbon::now());

        $result = $component->render();

        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $result);
    }
}
