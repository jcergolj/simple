<?php

namespace App\Enums;

enum TimeFormat: string
{
    case Hour12 = '12';
    case Hour24 = '24';

    /** Get the time format string for PHP's date() function */
    public function timeFormat(): string
    {
        return match ($this) {
            self::Hour12 => 'g:i A',
            self::Hour24 => 'H:i',
        };
    }

    /** Get the datetime format string combined with a date format */
    public function datetimeFormat(DateFormat $dateFormat): string
    {
        return $dateFormat->dateFormat().' '.$this->timeFormat();
    }

    /** Get the display name */
    public function name(): string
    {
        return match ($this) {
            self::Hour12 => '12-hour (2:30 PM)',
            self::Hour24 => '24-hour (14:30)',
        };
    }

    /** Get example time */
    public function example(): string
    {
        $time = now()->setTime(14, 30, 0);

        return $time->format($this->timeFormat());
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
        return self::Hour12;
    }
}
