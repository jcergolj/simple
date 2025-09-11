@props([
    'name' => 'amount',
    'currencyName' => 'currency',
    'value' => '',
    'currency' => 'USD',
    'placeholder' => '0.00',
    'required' => false,
    'id' => null,
    'useCommonCurrencies' => true
])

@php
    $id = $id ?? $name;
    $currencyOptions = $useCommonCurrencies
        ? App\Enums\Currency::commonOptions()
        : App\Enums\Currency::options();
@endphp

<div class="join w-full">
    <div class="relative flex-1">
        <input
            type="number"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            class="input input-bordered join-item w-full pl-12 focus:input-primary @error($name) input-error @enderror"
            step="0.01"
            min="0"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => '']) }}
        />
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
        </div>
    </div>
    <select
        name="{{ $currencyName }}"
        class="select select-bordered join-item focus:select-primary @error($currencyName) select-error @enderror"
    >
        @foreach($currencyOptions as $code => $display)
            <option value="{{ $code }}" {{ old($currencyName, $currency) === $code ? 'selected' : '' }}>
                {{ $display }}
            </option>
        @endforeach
    </select>
</div>