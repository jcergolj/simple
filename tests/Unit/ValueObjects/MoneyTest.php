<?php

namespace Tests\Unit\ValueObjects;

use App\Enums\Currency;
use App\ValueObjects\Money;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\ValueObjects\Money::class)]
class MoneyTest extends TestCase
{
    #[Test]
    public function constructor_creates_money_with_amount_and_currency(): void
    {
        $money = new Money(1000, Currency::USD);

        $this->assertSame(1000, $money->amount);
        $this->assertSame(Currency::USD, $money->currency);
    }

    #[Test]
    public function constructor_defaults_to_usd_currency(): void
    {
        $money = new Money(1000);

        $this->assertSame(1000, $money->amount);
        $this->assertSame(Currency::USD, $money->currency);
    }

    #[Test]
    public function from_creates_money_from_array_with_amount_and_currency(): void
    {
        $data = [
            'amount' => 2500,
            'currency' => 'EUR',
        ];

        $money = Money::from($data);

        $this->assertSame(2500, $money->amount);
        $this->assertSame(Currency::EUR, $money->currency);
    }

    #[Test]
    public function from_defaults_to_zero_amount_when_missing(): void
    {
        $data = ['currency' => 'GBP'];

        $money = Money::from($data);

        $this->assertSame(0, $money->amount);
        $this->assertSame(Currency::GBP, $money->currency);
    }

    #[Test]
    public function from_defaults_to_usd_currency_when_missing(): void
    {
        $data = ['amount' => 1500];

        $money = Money::from($data);

        $this->assertSame(1500, $money->amount);
        $this->assertSame(Currency::USD, $money->currency);
    }

    #[Test]
    public function from_handles_empty_array(): void
    {
        $money = Money::from([]);

        $this->assertSame(0, $money->amount);
        $this->assertSame(Currency::USD, $money->currency);
    }

    #[Test]
    public function from_decimal_creates_money_from_float_amount(): void
    {
        $money = Money::fromDecimal(25.99, Currency::EUR);

        $this->assertSame(2599, $money->amount);
        $this->assertSame(Currency::EUR, $money->currency);
    }

    #[Test]
    public function from_decimal_handles_string_currency(): void
    {
        $money = Money::fromDecimal(10.50, 'GBP');

        $this->assertSame(1050, $money->amount);
        $this->assertSame(Currency::GBP, $money->currency);
    }

    #[Test]
    public function from_decimal_defaults_to_usd_currency(): void
    {
        $money = Money::fromDecimal(15.75);

        $this->assertSame(1575, $money->amount);
        $this->assertSame(Currency::USD, $money->currency);
    }

    #[Test]
    public function from_decimal_rounds_to_nearest_cent(): void
    {
        $money = Money::fromDecimal(10.999, Currency::USD);

        $this->assertSame(1100, $money->amount);
    }

    #[Test]
    public function from_decimal_handles_zero(): void
    {
        $money = Money::fromDecimal(0.0, Currency::JPY);

        $this->assertSame(0, $money->amount);
        $this->assertSame(Currency::JPY, $money->currency);
    }

    #[Test]
    public function to_array_returns_correct_structure(): void
    {
        $money = new Money(3000, Currency::CAD);

        $array = $money->toArray();

        $this->assertSame([
            'amount' => 3000,
            'currency' => 'CAD',
        ], $array);
    }

    #[Test]
    public function json_serialize_returns_same_as_to_array(): void
    {
        $money = new Money(1234, Currency::CHF);

        $this->assertSame($money->toArray(), $money->jsonSerialize());
    }

    #[Test]
    public function formatted_displays_with_currency_symbol(): void
    {
        $money = new Money(1050, Currency::USD);

        $this->assertSame('$10.50', $money->formatted());
    }

    #[Test]
    public function formatted_handles_zero_amount(): void
    {
        $money = new Money(0, Currency::EUR);

        $this->assertSame('€0.00', $money->formatted());
    }

    #[Test]
    public function formatted_handles_large_amounts(): void
    {
        $money = new Money(123456789, Currency::USD);

        $this->assertSame('$1,234,567.89', $money->formatted());
    }

    #[Test]
    public function formatted_for_csv_displays_without_comma_separators(): void
    {
        $money = new Money(123456789, Currency::USD);

        $this->assertSame('$1234567.89', $money->formattedForCsv());
    }

    #[Test]
    public function formatted_for_csv_handles_small_amounts(): void
    {
        $money = new Money(1050, Currency::EUR);

        $this->assertSame('€10.50', $money->formattedForCsv());
    }

    #[Test]
    public function to_decimal_converts_cents_to_decimal(): void
    {
        $money = new Money(2550, Currency::USD);

        $this->assertEqualsWithDelta(25.50, $money->toDecimal(), PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function to_decimal_handles_zero(): void
    {
        $money = new Money(0, Currency::USD);

        $this->assertEqualsWithDelta(0.0, $money->toDecimal(), PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function to_decimal_handles_single_cents(): void
    {
        $money = new Money(1, Currency::USD);

        $this->assertEqualsWithDelta(0.01, $money->toDecimal(), PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function equals_returns_true_for_same_amount_and_currency(): void
    {
        $money1 = new Money(1000, Currency::USD);
        $money2 = new Money(1000, Currency::USD);

        $this->assertTrue($money1->equals($money2));
    }

    #[Test]
    public function equals_returns_false_for_different_amounts(): void
    {
        $money1 = new Money(1000, Currency::USD);
        $money2 = new Money(1001, Currency::USD);

        $this->assertFalse($money1->equals($money2));
    }

    #[Test]
    public function equals_returns_false_for_different_currencies(): void
    {
        $money1 = new Money(1000, Currency::USD);
        $money2 = new Money(1000, Currency::EUR);

        $this->assertFalse($money1->equals($money2));
    }

    #[Test]
    public function equals_returns_false_for_different_amount_and_currency(): void
    {
        $money1 = new Money(1000, Currency::USD);
        $money2 = new Money(2000, Currency::EUR);

        $this->assertFalse($money1->equals($money2));
    }

    #[Test]
    public function implements_arrayable_interface(): void
    {
        $money = new Money(1000, Currency::USD);

        $this->assertInstanceOf(\Illuminate\Contracts\Support\Arrayable::class, $money);
    }

    #[Test]
    public function implements_json_serializable_interface(): void
    {
        $money = new Money(1000, Currency::USD);

        $this->assertInstanceOf(\JsonSerializable::class, $money);
    }

    #[Test]
    public function can_be_json_encoded(): void
    {
        $money = new Money(1500, Currency::EUR);

        $json = json_encode($money);

        $this->assertSame('{"amount":1500,"currency":"EUR"}', $json);
    }

    #[Test]
    public function from_handles_string_amounts(): void
    {
        $data = [
            'amount' => '2500',
            'currency' => 'USD',
        ];

        $money = Money::from($data);

        $this->assertSame(2500, $money->amount);
    }

    #[Test]
    public function from_decimal_handles_negative_amounts(): void
    {
        $money = Money::fromDecimal(-10.50, Currency::USD);

        $this->assertSame(-1050, $money->amount);
        $this->assertSame(Currency::USD, $money->currency);
    }

    #[Test]
    public function formatted_handles_negative_amounts(): void
    {
        $money = new Money(-1050, Currency::USD);

        $this->assertSame('$-10.50', $money->formatted());
    }

    #[Test]
    public function to_decimal_handles_negative_amounts(): void
    {
        $money = new Money(-1050, Currency::USD);

        $this->assertSame(-10.50, $money->toDecimal());
    }
}
