<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class ProfileController extends Controller
{
    /** Shows the update profile info form. */
    public function edit(Request $request): View
    {
        return view('settings.profile.edit', [
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ]);
    }

    /** Handles the profile settings submit. */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        InAppNotification::success(__('Profile updated.'));

        return to_route('settings.profile.edit');
    }

    /** Shows the delete account form. */
    public function delete(): View
    {
        return view('settings.profile.delete');
    }

    /** Handles the delete account form submit. */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        $user = $request->user();

        Auth::guard('web')->logout();

        $user->delete();

        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    }
}
