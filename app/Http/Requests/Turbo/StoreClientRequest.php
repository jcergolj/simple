<?php

namespace App\Http\Requests\Turbo;

use App\Http\Requests\AppFormRequest;

class StoreClientRequest extends AppFormRequest
{
    protected function getRedirectUrl(): string
    {
        return route('turbo.clients.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'hourly_rate_amount' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate_currency' => 'required_with:hourly_rate_amount|string|in:'.implode(',', array_column(\App\Enums\Currency::cases(), 'value')),
        ];
    }
}
