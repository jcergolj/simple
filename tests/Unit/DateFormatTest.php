<?php

namespace Tests\Unit;

use App\Enums\DateFormat;
use App\Enums\TimeFormat;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class DateFormatTest extends TestCase
{
    public function test_date_format_enum_returns_correct_formats(): void
    {
        $usFormat = DateFormat::US;
        $ukFormat = DateFormat::UK;
        $euFormat = DateFormat::EU;

        $this->assertSame('m/d/Y', $usFormat->dateFormat());
        $this->assertSame('d/m/Y', $ukFormat->dateFormat());
        $this->assertSame('d.m.Y', $euFormat->dateFormat());

        $this->assertSame('m/d/Y g:i A', $usFormat->datetimeFormat());
        $this->assertSame('d/m/Y H:i', $ukFormat->datetimeFormat());
        $this->assertSame('d.m.Y H:i', $euFormat->datetimeFormat());
    }

    public function test_date_format_enum_examples(): void
    {
        $testDate = Carbon::create(2025, 12, 25);

        $this->assertSame('12/25/2025', $testDate->format(DateFormat::US->dateFormat()));
        $this->assertSame('25/12/2025', $testDate->format(DateFormat::UK->dateFormat()));
        $this->assertSame('25.12.2025', $testDate->format(DateFormat::EU->dateFormat()));
    }

    public function test_date_format_options(): void
    {
        $options = DateFormat::options();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('us', $options);
        $this->assertArrayHasKey('uk', $options);
        $this->assertArrayHasKey('eu', $options);

        $this->assertStringContainsString('MM/DD/YYYY', (string) $options['us']);
        $this->assertStringContainsString('DD/MM/YYYY', (string) $options['uk']);
        $this->assertStringContainsString('DD.MM.YYYY', (string) $options['eu']);
    }

    public function test_time_format_enum_returns_correct_formats(): void
    {
        $hour12Format = TimeFormat::Hour12;
        $hour24Format = TimeFormat::Hour24;

        $this->assertSame('g:i A', $hour12Format->timeFormat());
        $this->assertSame('H:i', $hour24Format->timeFormat());
    }

    public function test_time_format_options(): void
    {
        $options = TimeFormat::options();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('12', $options);
        $this->assertArrayHasKey('24', $options);

        $this->assertStringContainsString('12-hour', (string) $options['12']);
        $this->assertStringContainsString('24-hour', (string) $options['24']);
    }

    public function test_date_and_time_format_combination(): void
    {
        $testDate = Carbon::create(2025, 12, 25, 14, 30);
        $dateFormat = DateFormat::EU;
        $timeFormat = TimeFormat::Hour24;

        $combined = $dateFormat->datetimeFormatWithTime($timeFormat);
        $this->assertSame('d.m.Y H:i', $combined);

        $formatted = $testDate->format($combined);
        $this->assertSame('25.12.2025 14:30', $formatted);
    }
}
