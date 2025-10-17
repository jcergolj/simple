<?php

namespace App\Http\Requests\Settings;

use App\Http\Requests\AppFormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends AppFormRequest
{
    protected function getRedirectUrl(): string
    {
        return url()->previous() ?: route('settings.password.edit');
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'The current password field is required.',
            'current_password.current_password' => 'The current password is incorrect.',
            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
