<?php

namespace App\Models;

use App\Casts\AsMoney;
use App\Enums\Currency;
use App\ValueObjects\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property Money $rate
 */
class HourlyRate extends Model
{
    protected $fillable = [
        'amount',
        'currency',
        'rateable_id',
        'rateable_type',
        'rate',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'rateable_id' => 'integer',
            'currency' => Currency::class,
            'rate' => AsMoney::class,
        ];
    }

    public function rateable(): MorphTo
    {
        return $this->morphTo();
    }

    public function toMoney(): Money
    {
        return $this->rate;
    }

    public function formatted(): string
    {
        return $this->rate->formatted();
    }

    public static function createFromDecimal(float $amount, Currency|string $currency, $rateable): self
    {
        $money = Money::fromDecimal($amount, $currency);

        return self::create([
            'rate' => $money,
            'rateable_id' => $rateable->id,
            'rateable_type' => $rateable::class,
        ]);
    }
}
