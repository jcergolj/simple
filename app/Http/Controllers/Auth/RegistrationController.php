<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\ValueObjects\Money;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegistrationController extends Controller
{
    /** Shows registration form. */
    public function create()
    {
        // Check if any user already exists (only one user allowed in v1)
        // Skip this check in testing environment
        if (! app()->environment('testing') && User::exists()) {
            abort(403, 'Registration is closed. Only one user is allowed in v1.');
        }

        return view('auth.register');
    }

    /** Handles registration form submit. */
    public function store(Request $request)
    {
        // Check if any user already exists (only one user allowed in v1)
        // Skip this check in testing environment
        if (! app()->environment('testing') && User::exists()) {
            abort(403, 'Registration is closed. Only one user is allowed in v1.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'hourly_rate_amount' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate_currency' => 'required_with:hourly_rate_amount|string|in:'.implode(',', array_column(\App\Enums\Currency::cases(), 'value')),
        ], [
            'hourly_rate_amount.numeric' => 'Hourly rate must be a valid number.',
            'hourly_rate_amount.min' => 'Hourly rate must be at least 0.',
            'hourly_rate_currency.required_with' => 'Currency is required when hourly rate is specified.',
        ]);

        $hourlyRate = null;
        if ($request->input('hourly_rate_amount')) {
            $hourlyRate = new Money(
                amount: (float) $request->input('hourly_rate_amount'),
                currency: $request->input('hourly_rate_currency', 'USD')
            );
        }

        event(new Registered(($user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'hourly_rate' => $hourlyRate,
        ]))));

        Auth::login($user);

        return redirect()->intended(default: route('dashboard', absolute: false));
    }
}
