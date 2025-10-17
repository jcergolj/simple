<?php

namespace App\Http\Controllers\Settings;

use App\Enums\DateFormat;
use App\Enums\TimeFormat;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdatePreferencesRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class PreferencesController extends Controller
{
    /** Shows the preferences form. */
    public function edit(Request $request): View
    {
        return view('settings.preferences.edit', [
            'dateFormat' => $request->user()->getPreferredDateFormat(),
            'timeFormat' => $request->user()->getPreferredTimeFormat(),
            'dateFormatOptions' => DateFormat::options(),
            'timeFormatOptions' => TimeFormat::options(),
            'hourly_rate' => $request->user()->hourlyRate?->toMoney(),
        ]);
    }

    /** Handles the preferences form submit. */
    public function update(UpdatePreferencesRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $user->update([
            'date_format' => $validated['date_format'],
            'time_format' => $validated['time_format'],
        ]);

        if (isset($validated['hourly_rate_amount']) && $validated['hourly_rate_amount']) {
            $money = \App\ValueObjects\Money::fromDecimal(
                (float) $validated['hourly_rate_amount'],
                $validated['hourly_rate_currency'] ?? 'USD'
            );

            $user->hourlyRate()->updateOrCreate(
                ['rateable_id' => $user->id, 'rateable_type' => User::class],
                ['rate' => $money]
            );
        } elseif (isset($validated['hourly_rate_amount'])) {
            $user->hourlyRate()->delete();
        }

        InAppNotification::success(__('Preferences updated.'));

        return to_route('settings.preferences.edit');
    }
}
