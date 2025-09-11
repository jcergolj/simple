<?php

namespace App\Http\Requests\Turbo;

use App\Http\Requests\AppFormRequest;

class StoreTimeEntryRequest extends AppFormRequest
{
    protected function getRedirectUrl(): string
    {
        return route('turbo.time-entries.create');
    }

    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'hourly_rate_amount' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate_currency' => 'required_with:hourly_rate_amount|string|in:'.implode(',', array_column(\App\Enums\Currency::cases(), 'value')),
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required' => 'Start time is required.',
            'start_time.date' => 'Start time must be a valid date.',
            'end_time.date' => 'End time must be a valid date.',
            'end_time.after' => 'End time must be after start time.',
            'duration.integer' => 'Duration must be a valid number.',
            'duration.min' => 'Duration must be at least 0.',
            'client_id.exists' => 'Selected client does not exist.',
            'project_id.exists' => 'Selected project does not exist.',
            'hourly_rate_amount.numeric' => 'Hourly rate must be a valid number.',
            'hourly_rate_amount.min' => 'Hourly rate must be at least 0.',
            'hourly_rate_currency.required_with' => 'Currency is required when hourly rate is specified.',
        ];
    }
}
