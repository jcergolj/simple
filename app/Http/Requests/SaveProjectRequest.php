<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'client_id' => ['required', 'exists:clients,id'],
            'description' => ['nullable', 'string'],
            'hourly_rate_amount' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate_currency' => 'required_with:hourly_rate_amount|string|in:'.implode(',', array_column(\App\Enums\Currency::cases(), 'value')),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Project name is required.',
            'client_id.required' => 'Please select a client.',
            'client_id.exists' => 'Selected client does not exist.',
            'hourly_rate_amount.numeric' => 'Hourly rate must be a valid number.',
            'hourly_rate_amount.min' => 'Hourly rate must be at least 0.',
            'hourly_rate_currency.required_with' => 'Currency is required when hourly rate is specified.',
        ];
    }
}
