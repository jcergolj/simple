<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class ForgotPasswordController extends Controller
{
    /** Shows forgot password form. */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /** Handles forgot password form submit. */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($request->only('email'));

        InAppNotification::success(__('Reset link email was sent.'));

        return back();
    }
}
