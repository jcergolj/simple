<?php

namespace App\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class Money implements Arrayable, JsonSerializable
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency = 'USD'
    ) {}

    public static function from(array $data): self
    {
        return new self(
            amount: (float) ($data['amount'] ?? 0),
            currency: $data['currency'] ?? 'USD'
        );
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function formatted(): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency;

        return $symbol.number_format($this->amount, 2);
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }
}
