<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ConfirmPasswordController extends Controller
{
    /** Shows the password confirmation form. */
    public function create()
    {
        return view('auth.confirm-password');
    }

    /** Handles password confirmation form submit. */
    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        throw_unless(Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->input('password'),
        ]), ValidationException::withMessages([
            'password' => __('auth.password'),
        ]));

        session(['auth.password_confirmed_at' => \Carbon\Carbon::now()->getTimestamp()]);

        return redirect()->intended(default: route('dashboard', absolute: false));
    }
}
