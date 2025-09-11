<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\DateFormat;
use App\Enums\TimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'date_format',
        'time_format',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** Get the user's initials */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\MorphOne<HourlyRate, $this> */
    public function hourlyRate(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(HourlyRate::class, 'rateable');
    }

    /** Get the user's preferred date format or default */
    public function getPreferredDateFormat(): DateFormat
    {
        return DateFormat::from($this->date_format ?? DateFormat::default()->value);
    }

    /** Get the user's preferred time format or default */
    public function getPreferredTimeFormat(): TimeFormat
    {
        return TimeFormat::from($this->time_format ?? TimeFormat::default()->value);
    }

    /** Format a date according to user's preference */
    public function formatDate($date): string
    {
        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        return $date->format($this->getPreferredDateFormat()->dateFormat());
    }

    /** Format a time according to user's preference */
    public function formatTime($time): string
    {
        if (is_string($time)) {
            $time = \Carbon\Carbon::parse($time);
        }

        return $time->format($this->getPreferredTimeFormat()->timeFormat());
    }

    /** Format a datetime according to user's preference */
    public function formatDatetime($datetime): string
    {
        if (is_string($datetime)) {
            $datetime = \Carbon\Carbon::parse($datetime);
        }

        $dateFormat = $this->getPreferredDateFormat();
        $timeFormat = $this->getPreferredTimeFormat();

        return $datetime->format($dateFormat->datetimeFormatWithTime($timeFormat));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
