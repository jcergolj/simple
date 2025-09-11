<?php

namespace App\View\Components\Form;

use App\Enums\Currency;
use App\Models\Client;
use App\Models\Project;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MoneyInput extends Component
{
    public string $defaultCurrency;

    public string $defaultValue;

    public function __construct(
        public string $name = 'amount',
        public string $currencyName = 'currency',
        public ?string $value = null,
        public Currency|string|null $currency = null,
        public string $placeholder = '0.00',
        public bool $required = false,
        public ?string $id = null,
        public bool $useCommonCurrencies = true,
        public ?Project $project = null,
        public ?Client $client = null,
        public ?string $currencyAttributes = null
    ) {
        $this->defaultValue = $this->determineAmount();
        $this->value ??= $this->defaultValue;
        $this->defaultCurrency = $this->determineCurrency();
        $this->id ??= $this->name;
    }

    protected function determineCurrency(): string
    {
        // 1. First check for old input (form validation error recovery)
        $oldValue = old($this->currencyName);
        if ($oldValue !== null) {
            return (string) $oldValue;
        }

        // 2. Use explicitly passed currency if provided
        if ($this->currency !== null && $this->currency !== '' && $this->currency !== '0') {
            return $this->currency instanceof Currency ? $this->currency->value : $this->currency;
        }

        // 3. Try to get currency from project's hourly rate
        if ($this->project?->hourly_rate) {
            return $this->project->hourly_rate->currency->value;
        }

        // 4. Try to get currency from client's hourly rate
        if ($this->client?->hourly_rate) {
            return $this->client->hourly_rate->currency->value;
        }

        // 5. Try to get currency from project's client if project exists but has no rate
        if ($this->project?->client?->hourly_rate) {
            return $this->project->client->hourly_rate->currency->value;
        }

        // 6. Try to get currency from authenticated user's hourly rate
        if (auth()->check() && auth()->user()->hourlyRate) {
            return auth()->user()->hourlyRate->rate->currency->value;
        }

        // 7. Default to USD
        return Currency::USD->value;
    }

    protected function determineAmount(): string
    {
        if (old($this->name) !== null) {
            return old($this->name);
        }

        if ($this->value !== null) {
            return $this->value;
        }

        if ($this->project?->hourly_rate) {
            return (string) $this->project->hourly_rate->toDecimal();
        }

        if ($this->client?->hourly_rate) {
            return (string) $this->client->hourly_rate->toDecimal();
        }

        if ($this->project?->client?->hourly_rate) {
            return (string) $this->project->client->hourly_rate->toDecimal();
        }

        if (auth()->check() && auth()->user()->hourlyRate) {
            return (string) auth()->user()->hourlyRate->rate->toDecimal();
        }

        return '';
    }

    public function currencyOptions(): array
    {
        return $this->useCommonCurrencies
            ? Currency::commonOptions()
            : Currency::options();
    }

    public function render(): View|Closure|string
    {
        return view('components.form.money-input');
    }
}
