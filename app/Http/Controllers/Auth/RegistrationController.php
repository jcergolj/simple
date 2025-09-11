<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Currency;
use App\Http\Controllers\Controller;
use App\Models\HourlyRate;
use App\Models\User;
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

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'hourly_rate_amount' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate_currency' => ['required_with:hourly_rate_amount', 'string', 'in:'.implode(',', array_column(Currency::cases(), 'value'))],
        ]);

        event(new Registered(($user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]))));

        // Save hourly rate if provided
        if (! empty($validated['hourly_rate_amount'])) {
            $money = \App\ValueObjects\Money::fromDecimal(
                (float) $validated['hourly_rate_amount'],
                $validated['hourly_rate_currency'] ?? 'USD'
            );

            HourlyRate::create([
                'rate' => $money,
                'rateable_id' => $user->id,
                'rateable_type' => User::class,
            ]);
        }

        Auth::login($user);

        return redirect()->intended(default: route('dashboard', absolute: false));
    }
}
