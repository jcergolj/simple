<?php

namespace App\Casts;

use App\Enums\Currency;
use App\ValueObjects\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class AsMoney implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Money
    {
        // Handle multi-column format (amount + currency columns)
        $amountKey = $key === 'rate' ? 'amount' : $key.'_amount';
        $currencyKey = $key === 'rate' ? 'currency' : $key.'_currency';

        if (isset($attributes[$amountKey]) && isset($attributes[$currencyKey])) {
            $currency = $attributes[$currencyKey];
            if (is_string($currency)) {
                $currency = Currency::from($currency);
            }

            return new Money(
                amount: (int) $attributes[$amountKey],
                currency: $currency
            );
        }

        // Handle single-column JSON format
        if ($value === null) {
            return null;
        }

        $data = json_decode((string) $value, true);

        if (! is_array($data)) {
            return null;
        }

        return Money::from($data);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array|string|null
    {
        if ($value === null) {
            // For multi-column format
            $amountKey = $key === 'rate' ? 'amount' : $key.'_amount';
            $currencyKey = $key === 'rate' ? 'currency' : $key.'_currency';

            if (array_key_exists($amountKey, $model->getAttributes()) || array_key_exists($currencyKey, $model->getAttributes())) {
                return [
                    $amountKey => null,
                    $currencyKey => null,
                ];
            }

            // For single-column JSON format
            return null;
        }

        throw_unless($value instanceof Money, InvalidArgumentException::class, 'The given value is not a Money instance.');

        // Detect storage format by checking if amount/currency columns exist
        $amountKey = $key === 'rate' ? 'amount' : $key.'_amount';
        $currencyKey = $key === 'rate' ? 'currency' : $key.'_currency';

        // Check if this is a multi-column setup by seeing if the columns exist in fillable or the table
        $fillable = $model->getFillable();
        if (in_array($amountKey, $fillable) || in_array($currencyKey, $fillable)) {
            return [
                $amountKey => $value->amount,
                $currencyKey => $value->currency->value,
            ];
        }

        // Default to single-column JSON format
        return json_encode($value->toArray());
    }
}
