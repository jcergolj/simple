<?php

namespace App\Http\Requests\Settings;

use App\Http\Requests\AppFormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends AppFormRequest
{
    protected function getRedirectUrl(): string
    {
        return route('settings.profile.edit');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()?->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'email.max' => 'The email must not exceed 255 characters.',
        ];
    }
}
