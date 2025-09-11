<?php

namespace App\Enums;

enum DateFormat: string
{
    case US = 'us';
    case UK = 'uk';
    case EU = 'eu';

    /** Get the date format string for PHP's date() function */
    public function dateFormat(): string
    {
        return match ($this) {
            self::US => 'm/d/Y',
            self::UK => 'd/m/Y',
            self::EU => 'd.m.Y',
        };
    }

    /** Get the datetime format string for PHP's date() function */
    public function datetimeFormat(): string
    {
        return match ($this) {
            self::US => $this->dateFormat().' g:i A',
            self::UK => $this->dateFormat().' H:i',
            self::EU => $this->dateFormat().' H:i',
        };
    }

    /** Get the datetime format string combined with a time format */
    public function datetimeFormatWithTime(TimeFormat $timeFormat): string
    {
        return $this->dateFormat().' '.$timeFormat->timeFormat();
    }

    /** Get the HTML input format for date inputs */
    public function inputFormat(): string
    {
        return match ($this) {
            self::US => 'Y-m-d',  // HTML date inputs always use ISO format
            self::UK => 'Y-m-d',
            self::EU => 'Y-m-d',
        };
    }

    /** Get the display name */
    public function name(): string
    {
        return match ($this) {
            self::US => 'US (MM/DD/YYYY)',
            self::UK => 'UK (DD/MM/YYYY)',
            self::EU => 'EU (DD.MM.YYYY)',
        };
    }

    /** Get example date */
    public function example(): string
    {
        $date = now()->setDate(2024, 12, 25);

        return $date->format($this->dateFormat());
    }

    /** Get all formats as options for forms */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $format) {
            $options[(string) $format->value] = $format->name().' - '.$format->example();
        }

        return $options;
    }

    /** Get default format */
    public static function default(): self
    {
        return self::US;
    }
}
