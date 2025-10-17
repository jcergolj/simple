<?php

namespace Tests\Unit\View\Components\Form;

use App\Enums\Currency;
use App\Models\Client;
use App\Models\HourlyRate;
use App\Models\Project;
use App\Models\User;
use App\ValueObjects\Money;
use App\View\Components\Form\MoneyInput;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\View\Components\Form\MoneyInput::class)]
class MoneyInputTest extends TestCase
{
    #[Test]
    public function constructor_sets_default_values(): void
    {
        $component = new MoneyInput;

        $this->assertSame('amount', $component->name);
        $this->assertSame('currency', $component->currencyName);
        $this->assertSame('0.00', $component->placeholder);
        $this->assertFalse($component->required);
        $this->assertTrue($component->useCommonCurrencies);
        $this->assertSame('amount', $component->id);
    }

    #[Test]
    public function constructor_accepts_custom_parameters(): void
    {
        $component = new MoneyInput(
            name: 'hourly_rate',
            currencyName: 'rate_currency',
            value: '50.00',
            currency: Currency::EUR,
            placeholder: 'Enter amount',
            required: true,
            id: 'custom-id',
            useCommonCurrencies: false
        );

        $this->assertSame('hourly_rate', $component->name);
        $this->assertSame('rate_currency', $component->currencyName);
        $this->assertSame('50.00', $component->value);
        $this->assertSame(Currency::EUR, $component->currency);
        $this->assertSame('Enter amount', $component->placeholder);
        $this->assertTrue($component->required);
        $this->assertSame('custom-id', $component->id);
        $this->assertFalse($component->useCommonCurrencies);
    }

    #[Test]
    public function determine_currency_returns_usd_as_default(): void
    {
        Auth::shouldReceive('check')->andReturn(false);

        $component = new MoneyInput;

        $this->assertSame('USD', $component->defaultCurrency);
    }

    #[Test]
    public function determine_currency_uses_explicit_currency(): void
    {
        $component = new MoneyInput(currency: Currency::EUR);

        $this->assertSame('EUR', $component->defaultCurrency);
    }

    #[Test]
    public function determine_currency_uses_string_currency(): void
    {
        $component = new MoneyInput(currency: 'GBP');

        $this->assertSame('GBP', $component->defaultCurrency);
    }

    #[Test]
    public function determine_currency_from_project_hourly_rate(): void
    {
        $project = new Project;
        $project->hourly_rate = new Money(5000, Currency::CAD);

        $component = new MoneyInput(project: $project);

        $this->assertSame('CAD', $component->defaultCurrency);
    }

    #[Test]
    public function determine_currency_from_client_hourly_rate(): void
    {
        $client = new Client;
        $client->hourly_rate = new Money(7500, Currency::CHF);

        $component = new MoneyInput(client: $client);

        $this->assertSame('CHF', $component->defaultCurrency);
    }

    #[Test]
    public function determine_currency_from_project_client(): void
    {
        $client = new Client;
        $client->hourly_rate = new Money(6000, Currency::JPY);

        $project = new Project;
        $project->setRelation('client', $client);

        $component = new MoneyInput(project: $project);

        $this->assertSame('JPY', $component->defaultCurrency);
    }

    #[Test]
    public function determine_currency_from_authenticated_user(): void
    {
        $user = User::factory()->make();
        $hourlyRate = new HourlyRate;
        $hourlyRate->rate = new Money(10000, Currency::AUD);
        $user->setRelation('hourlyRate', $hourlyRate);

        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);

        $component = new MoneyInput;

        $this->assertSame('AUD', $component->defaultCurrency);
    }

    #[Test]
    public function determine_amount_returns_empty_as_default(): void
    {
        Auth::shouldReceive('check')->andReturn(false);

        $component = new MoneyInput;

        $this->assertSame('', $component->defaultValue);
    }

    #[Test]
    public function determine_amount_uses_explicit_value(): void
    {
        $component = new MoneyInput(value: '42.50');

        $this->assertSame('42.50', $component->value);
    }

    #[Test]
    public function determine_amount_from_project_hourly_rate(): void
    {
        $project = new Project;
        $project->hourly_rate = new Money(7500, Currency::USD); // $75.00

        $component = new MoneyInput(project: $project);

        $this->assertSame('75', $component->defaultValue);
    }

    #[Test]
    public function determine_amount_from_client_hourly_rate(): void
    {
        $client = new Client;
        $client->hourly_rate = new Money(12500, Currency::USD); // $125.00

        $component = new MoneyInput(client: $client);

        $this->assertSame('125', $component->defaultValue);
    }

    #[Test]
    public function determine_amount_from_project_client(): void
    {
        $client = new Client;
        $client->hourly_rate = new Money(9000, Currency::USD); // $90.00

        $project = new Project;
        $project->setRelation('client', $client);

        $component = new MoneyInput(project: $project);

        $this->assertSame('90', $component->defaultValue);
    }

    #[Test]
    public function determine_amount_from_authenticated_user(): void
    {
        $user = User::factory()->make();
        $hourlyRate = new HourlyRate;
        $hourlyRate->rate = new Money(15000, Currency::USD); // $150.00
        $user->setRelation('hourlyRate', $hourlyRate);

        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);

        $component = new MoneyInput;

        $this->assertSame('150', $component->defaultValue);
    }

    #[Test]
    public function currency_options_returns_common_currencies_by_default(): void
    {
        $component = new MoneyInput;

        $options = $component->currencyOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('USD', $options);
        $this->assertArrayHasKey('EUR', $options);
        $this->assertCount(10, $options); // Common currencies count
    }

    #[Test]
    public function currency_options_returns_all_currencies_when_disabled(): void
    {
        $component = new MoneyInput(useCommonCurrencies: false);

        $options = $component->currencyOptions();

        $this->assertIsArray($options);
        $this->assertGreaterThan(10, count($options)); // Should have more than common currencies
    }

    #[Test]
    public function render_returns_view(): void
    {
        $component = new MoneyInput;

        $result = $component->render();

        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $result);
    }

    #[Test]
    public function project_takes_precedence_over_client(): void
    {
        $client = new Client;
        $client->hourly_rate = new Money(5000, Currency::EUR);

        $project = new Project;
        $project->hourly_rate = new Money(7500, Currency::GBP);

        $component = new MoneyInput(project: $project, client: $client);

        $this->assertSame('GBP', $component->defaultCurrency);
        $this->assertSame('75', $component->defaultValue);
    }

    #[Test]
    public function explicit_currency_takes_precedence_over_project(): void
    {
        $project = new Project;
        $project->hourly_rate = new Money(5000, Currency::CAD);

        $component = new MoneyInput(currency: Currency::SEK, project: $project);

        $this->assertSame('SEK', $component->defaultCurrency);
    }

    #[Test]
    public function explicit_value_takes_precedence_over_project(): void
    {
        $project = new Project;
        $project->hourly_rate = new Money(5000, Currency::USD);

        $component = new MoneyInput(value: '99.99', project: $project);

        $this->assertSame('99.99', $component->value);
    }
}
