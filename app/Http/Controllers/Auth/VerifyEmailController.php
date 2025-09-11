<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class VerifyEmailController extends Controller
{
    /** Shows the verify email page. */
    public function show()
    {
        return view('auth.verify-email');
    }

    /** Resends the email verification email. */
    public function store()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->intended(default: route('dashboard', absolute: false));
        }

        Auth::user()->sendEmailVerificationNotification();

        InAppNotification::success(__('Verification email was sent.'));

        return back()->with('verification-sent', true);
    }

    /** Mark the authenticated user's email address as verified. */
    public function update(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        if ($request->user()->markEmailAsVerified()) {
            /** @var \Illuminate\Contracts\Auth\MustVerifyEmail $user */
            $user = $request->user();

            event(new Verified($user));
        }

        InAppNotification::success(__('Email was verified.'));

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
