<?php

namespace App\Http\Controllers\Turbo;

use App\Enums\Currency;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\ValueObjects\Money;
use Illuminate\Http\Request;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class ClientSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q', '');

        if (empty($query) || strlen((string) $query) < 2) {
            return response('', 200);
        }

        $clients = \App\Models\Client::where('name', 'like', '%'.$query.'%')
            ->limit(10)
            ->get();

        return view('turbo::clients-search.index', ['clients' => $clients, 'query' => $query]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_hourly_rate_amount' => ['nullable', 'numeric', 'min:0'],
            'client_hourly_rate_currency' => 'required_with:client_hourly_rate_amount|string|in:'.implode(',', array_column(Currency::cases(), 'value')),
        ], [
            'client_name.required' => 'Client name is required.',
            'client_hourly_rate_amount.numeric' => 'Hourly rate must be a valid number.',
            'client_hourly_rate_amount.min' => 'Hourly rate must be at least 0.',
            'client_hourly_rate_currency.required_with' => 'Currency is required when hourly rate is specified.',
        ]);

        $hourlyRate = null;
        if ($request->input('client_hourly_rate_amount')) {
            $hourlyRate = new Money(
                amount: (float) $request->input('client_hourly_rate_amount'),
                currency: $request->input('client_hourly_rate_currency', 'USD')
            );
        }

        $client = Client::create([
            'name' => $request->input('client_name'),
            'hourly_rate' => $hourlyRate,
        ]);

        InAppNotification::success('Client "'.$client->name.'" created successfully!');

        return to_route('dashboard', [
            'client_id' => $client->id,
            'client_name' => $client->name,
        ]);
    }
}
