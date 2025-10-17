<?php

namespace Tests\Unit\Casts;

use App\Casts\AsMoney;
use App\Enums\Currency;
use App\Models\Project;
use App\ValueObjects\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Casts\AsMoney::class)]
class AsMoneyTest extends TestCase
{
    private AsMoney $cast;

    private Project $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cast = new AsMoney;
        $this->model = new Project;
    }

    #[Test]
    public function implements_casts_attributes_interface(): void
    {
        $this->assertInstanceOf(CastsAttributes::class, $this->cast);
    }

    #[Test]
    public function get_returns_null_for_null_value(): void
    {
        $result = $this->cast->get($this->model, 'hourly_rate', null, []);

        $this->assertNotInstanceOf(\App\ValueObjects\Money::class, $result);
    }

    #[Test]
    public function get_handles_multi_column_format_with_rate_key(): void
    {
        $attributes = [
            'amount' => 5000,
            'currency' => 'USD',
        ];

        $result = $this->cast->get($this->model, 'rate', null, $attributes);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertSame(5000, $result->amount);
        $this->assertSame(Currency::USD, $result->currency);
    }

    #[Test]
    public function get_handles_multi_column_format_with_custom_key(): void
    {
        $attributes = [
            'hourly_rate_amount' => 7500,
            'hourly_rate_currency' => 'EUR',
        ];

        $result = $this->cast->get($this->model, 'hourly_rate', null, $attributes);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertSame(7500, $result->amount);
        $this->assertSame(Currency::EUR, $result->currency);
    }

    #[Test]
    public function get_handles_multi_column_format_with_currency_enum(): void
    {
        $attributes = [
            'amount' => 3000,
            'currency' => Currency::GBP,
        ];

        $result = $this->cast->get($this->model, 'rate', null, $attributes);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertSame(3000, $result->amount);
        $this->assertSame(Currency::GBP, $result->currency);
    }

    #[Test]
    public function get_handles_single_column_json_format(): void
    {
        $jsonValue = json_encode(['amount' => 2500, 'currency' => 'CAD']);

        $result = $this->cast->get($this->model, 'hourly_rate', $jsonValue, []);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertSame(2500, $result->amount);
        $this->assertSame(Currency::CAD, $result->currency);
    }

    #[Test]
    public function get_returns_null_for_invalid_json(): void
    {
        $result = $this->cast->get($this->model, 'hourly_rate', 'invalid-json', []);

        $this->assertNotInstanceOf(\App\ValueObjects\Money::class, $result);
    }

    #[Test]
    public function get_returns_null_for_non_array_json(): void
    {
        $result = $this->cast->get($this->model, 'hourly_rate', '"string-value"', []);

        $this->assertNotInstanceOf(\App\ValueObjects\Money::class, $result);
    }

    #[Test]
    public function set_throws_exception_for_non_money_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The given value is not a Money instance.');

        $this->cast->set($this->model, 'hourly_rate', 'invalid-value', []);
    }

    #[Test]
    public function set_returns_null_for_null_value_single_column(): void
    {
        $result = $this->cast->set($this->model, 'hourly_rate', null, []);

        $this->assertNull($result);
    }

    #[Test]
    public function set_returns_array_for_null_value_multi_column_rate(): void
    {
        // Mock a model that has amount/currency attributes
        $model = $this->createMockModel(['amount', 'currency'], ['amount' => 100, 'currency' => 'USD']);

        $result = $this->cast->set($model, 'rate', null, []);

        $this->assertIsArray($result);
        $this->assertSame([
            'amount' => null,
            'currency' => null,
        ], $result);
    }

    #[Test]
    public function set_returns_array_for_null_value_multi_column_custom(): void
    {
        $model = $this->createMockModel(['hourly_rate_amount', 'hourly_rate_currency'], ['hourly_rate_amount' => 100]);

        $result = $this->cast->set($model, 'hourly_rate', null, []);

        $this->assertIsArray($result);
        $this->assertSame([
            'hourly_rate_amount' => null,
            'hourly_rate_currency' => null,
        ], $result);
    }

    #[Test]
    public function set_returns_multi_column_array_when_fillable_contains_amount_currency(): void
    {
        $money = new Money(4000, Currency::USD);
        $model = $this->createMockModel(['amount', 'currency']);

        $result = $this->cast->set($model, 'rate', $money, []);

        $this->assertIsArray($result);
        $this->assertSame([
            'amount' => 4000,
            'currency' => 'USD',
        ], $result);
    }

    #[Test]
    public function set_returns_multi_column_array_when_fillable_contains_custom_columns(): void
    {
        $money = new Money(6000, Currency::EUR);
        $model = $this->createMockModel(['hourly_rate_amount']);

        $result = $this->cast->set($model, 'hourly_rate', $money, []);

        $this->assertIsArray($result);
        $this->assertSame([
            'hourly_rate_amount' => 6000,
            'hourly_rate_currency' => 'EUR',
        ], $result);
    }

    #[Test]
    public function set_returns_json_string_for_single_column_format(): void
    {
        $money = new Money(3500, Currency::GBP);
        $model = $this->createMockModel(['hourly_rate']);

        $result = $this->cast->set($model, 'hourly_rate', $money, []);

        $this->assertIsString($result);
        $this->assertSame('{"amount":3500,"currency":"GBP"}', $result);
    }

    #[Test]
    public function get_and_set_roundtrip_works_for_json_format(): void
    {
        $originalMoney = new Money(1250, Currency::JPY);
        $model = $this->createMockModel(['hourly_rate']);

        $jsonValue = $this->cast->set($model, 'hourly_rate', $originalMoney, []);
        $retrievedMoney = $this->cast->get($this->model, 'hourly_rate', $jsonValue, []);

        $this->assertTrue($originalMoney->equals($retrievedMoney));
    }

    #[Test]
    public function get_and_set_roundtrip_works_for_multi_column_format(): void
    {
        $originalMoney = new Money(8750, Currency::CHF);
        $model = $this->createMockModel(['amount', 'currency']);

        $attributes = $this->cast->set($model, 'rate', $originalMoney, []);
        $retrievedMoney = $this->cast->get($this->model, 'rate', null, $attributes);

        $this->assertTrue($originalMoney->equals($retrievedMoney));
    }

    #[Test]
    public function handles_zero_amount_money(): void
    {
        $money = new Money(0, Currency::USD);
        $model = $this->createMockModel(['hourly_rate']);

        $jsonValue = $this->cast->set($model, 'hourly_rate', $money, []);
        $result = $this->cast->get($this->model, 'hourly_rate', $jsonValue, []);

        $this->assertSame(0, $result->amount);
        $this->assertSame(Currency::USD, $result->currency);
    }

    #[Test]
    public function handles_large_amount_money(): void
    {
        $money = new Money(999999999, Currency::USD);
        $model = $this->createMockModel(['amount', 'currency']);

        $attributes = $this->cast->set($model, 'rate', $money, []);
        $result = $this->cast->get($this->model, 'rate', null, $attributes);

        $this->assertSame(999999999, $result->amount);
        $this->assertSame(Currency::USD, $result->currency);
    }

    /** Create a mock model with specified fillable attributes and existing attributes */
    private function createMockModel(array $fillable, array $attributes = []): \PHPUnit\Framework\MockObject\MockObject
    {
        $model = $this->createMock(Project::class);
        $model->method('getFillable')->willReturn($fillable);
        $model->method('getAttributes')->willReturn($attributes);

        return $model;
    }
}
