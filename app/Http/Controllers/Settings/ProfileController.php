<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class ProfileController extends Controller
{
    /** Shows the update profile info form. */
    public function edit(Request $request)
    {
        return view('settings.profile.edit', [
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ]);
    }

    /** Handles the profile settings submit. */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        InAppNotification::success(__('Profile updated.'));

        return back();
    }

    /** Shows the delete account form. */
    public function delete()
    {
        return view('settings.profile.delete');
    }

    /** Handles the delete account form submit. */
    public function destroy(Request $request)
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
