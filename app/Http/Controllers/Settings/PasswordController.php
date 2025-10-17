<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class PasswordController extends Controller
{
    /** Shows the update password form. */
    public function edit(): View
    {
        return view('settings.password.edit');
    }

    /** Handles the update password form submit. */
    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        InAppNotification::success(__('Password updated.'));

        return to_route('settings.password.edit');
    }
}
