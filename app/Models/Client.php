<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 * @property \App\ValueObjects\Money|null $hourly_rate
 */
class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'hourly_rate',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => \App\Casts\AsMoney::class,
        ];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function calculateTotalEarnings(): ?\App\ValueObjects\Money
    {
        $timeEntries = $this->timeEntries()->with(['project', 'client'])->whereNotNull('end_time')->get();

        if ($timeEntries->isEmpty()) {
            return null;
        }

        $totalAmount = 0;
        $currency = null;

        /** @var TimeEntry $entry */
        foreach ($timeEntries as $entry) {
            $earnings = $entry->calculateEarnings();

            if ($earnings instanceof \App\ValueObjects\Money) {
                $totalAmount += $earnings->amount;
                $currency ??= $earnings->currency;
            }
        }

        if (! $currency instanceof \App\Enums\Currency) {
            return null;
        }

        return new \App\ValueObjects\Money(
            amount: $totalAmount,
            currency: $currency
        );
    }
}
