<?php

namespace App\Models;

use App\Casts\AsMoney;
use App\ValueObjects\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \Carbon\Carbon $start_time
 * @property \Carbon\Carbon|null $end_time
 * @property int|null $duration
 * @property string|null $notes
 * @property int|null $client_id
 * @property int|null $project_id
 * @property \App\ValueObjects\Money|null $hourly_rate
 * @property Client|null $client
 * @property Project|null $project
 */
class TimeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_time',
        'end_time',
        'duration',
        'notes',
        'client_id',
        'project_id',
        'hourly_rate',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'hourly_rate' => AsMoney::class,
            'client_id' => 'integer',
            'project_id' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getEffectiveHourlyRate(): ?Money
    {
        return $this->hourly_rate
            ?? $this->project->hourly_rate
            ?? $this->client?->hourly_rate;
    }

    public function calculateEarnings(): ?Money
    {
        $rate = $this->getEffectiveHourlyRate();

        if (! $rate instanceof \App\ValueObjects\Money || ! $this->duration || $this->duration < 0) {
            return null;
        }

        $hours = $this->duration / 3600;

        return new Money(
            amount: (int) round($rate->amount * $hours),
            currency: $rate->currency
        );
    }

    public function getFormattedDuration(): string
    {
        if (! $this->duration || $this->duration < 0) {
            return '0m';
        }

        $hours = intval($this->duration / 3600);
        $minutes = intval(($this->duration % 3600) / 60);
        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        }

        if ($hours > 0) {
            return "{$hours}h";
        }

        return "{$minutes}m";
    }
}
