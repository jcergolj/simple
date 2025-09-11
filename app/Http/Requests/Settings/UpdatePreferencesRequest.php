<?php

namespace App\Http\Requests\Settings;

use App\Enums\DateFormat;
use App\Enums\TimeFormat;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferencesRequest extends FormRequest
{
    /** Determine if the user is authorized to make this request. */
    public function authorize(): bool
    {
        return true;
    }

    /** Get the validation rules that apply to the request. */
    public function rules(): array
    {
        return [
            'date_format' => ['required', 'string', 'in:'.implode(',', array_column(DateFormat::cases(), 'value'))],
            'time_format' => ['required', 'string', 'in:'.implode(',', array_column(TimeFormat::cases(), 'value'))],
            'hourly_rate_amount' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate_currency' => 'required_with:hourly_rate_amount|string|in:'.implode(',', array_column(\App\Enums\Currency::cases(), 'value')),
        ];
    }

    /** Get custom error messages for validation rules. */
    public function messages(): array
    {
        return [
            'date_format.required' => __('Please select a date format.'),
            'date_format.in' => __('The selected date format is invalid.'),
            'time_format.required' => __('Please select a time format.'),
            'time_format.in' => __('The selected time format is invalid.'),
            'hourly_rate_amount.numeric' => __('The hourly rate must be a valid number.'),
            'hourly_rate_amount.min' => __('The hourly rate must be at least 0.'),
            'hourly_rate_currency.required_with' => __('Please select a currency for the hourly rate.'),
            'hourly_rate_currency.in' => __('The selected currency is invalid.'),
        ];
    }
}
