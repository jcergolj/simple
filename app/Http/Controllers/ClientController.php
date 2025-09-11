<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\ValueObjects\Money;
use Illuminate\Http\Request;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::withCount(['projects', 'timeEntries'])->orderBy('name')->paginate(10);

        redirect()->redirectIfLastPageEmpty($request, $clients);

        return view('clients.index', ['clients' => $clients]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hourly_rate_amount' => 'nullable|numeric|min:0',
            'hourly_rate_currency' => 'required_with:hourly_rate_amount|string|in:'.implode(',', array_column(\App\Enums\Currency::cases(), 'value')),
        ]);

        $hourlyRate = null;
        if ($validated['hourly_rate_amount']) {
            $hourlyRate = new Money(
                amount: (float) $validated['hourly_rate_amount'],
                currency: $validated['hourly_rate_currency'] ?? 'USD'
            );
        }

        Client::create([
            'name' => $validated['name'],
            'hourly_rate' => $hourlyRate,
        ]);

        InAppNotification::success(__('New client successfully created.'));

        return to_intended_route('clients.index');
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hourly_rate_amount' => 'nullable|numeric|min:0',
            'hourly_rate_currency' => 'required_with:hourly_rate_amount|string|in:'.implode(',', array_column(\App\Enums\Currency::cases(), 'value')),
        ]);

        $hourlyRate = null;
        if ($validated['hourly_rate_amount']) {
            $hourlyRate = new Money(
                amount: (float) $validated['hourly_rate_amount'],
                currency: $validated['hourly_rate_currency'] ?? 'USD'
            );
        }

        $client->update([
            'name' => $validated['name'],
            'hourly_rate' => $hourlyRate,
        ]);

        InAppNotification::success(__('Client successfully updated.'));

        return to_intended_route('clients.index');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return to_intended_route('clients.index');
    }
}
