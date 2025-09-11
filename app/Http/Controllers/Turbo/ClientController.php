<?php

namespace App\Http\Controllers\Turbo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Turbo\StoreClientRequest;
use App\Http\Requests\Turbo\UpdateClientRequest;
use App\Models\Client;
use App\ValueObjects\Money;

class ClientController extends Controller
{
    public function create()
    {
        return view('turbo::clients.create');
    }

    public function store(StoreClientRequest $request)
    {
        $validated = $request->validated();

        $hourlyRate = null;
        if ($validated['hourly_rate_amount']) {
            $hourlyRate = Money::fromDecimal(
                amount: (float) $validated['hourly_rate_amount'],
                currency: $validated['hourly_rate_currency'] ?? 'USD'
            );
        }

        $client = Client::create([
            'name' => $validated['name'],
            'hourly_rate' => $hourlyRate,
        ]);

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return new \Illuminate\Http\JsonResponse([
                'success' => true,
                'client' => [
                    'id' => $client->id,
                    'name' => $client->name,
                    'hourly_rate' => $client->hourly_rate ? $client->hourly_rate->formatted() : null,
                ],
            ]);
        }

        // Fetch updated list with filters applied
        $query = Client::withCount(['projects', 'timeEntries']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', '%'.$search.'%');
        }

        $clients = $query->orderBy('name')->paginate(10)->withQueryString();

        return response()
            ->view('turbo::clients.store', [
                'clients' => $clients,
            ])
            ->header('Content-Type', 'text/vnd.turbo-stream.html');
    }

    public function edit(Client $client)
    {
        return view('turbo::clients.edit', ['client' => $client]);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $validated = $request->validated();

        $hourlyRate = null;
        if ($validated['hourly_rate_amount']) {
            $hourlyRate = Money::fromDecimal(
                amount: (float) $validated['hourly_rate_amount'],
                currency: $validated['hourly_rate_currency'] ?? 'USD'
            );
        }

        $client->update([
            'name' => $validated['name'],
            'hourly_rate' => $hourlyRate,
        ]);

        // Fetch updated list with filters applied
        $query = Client::withCount(['projects', 'timeEntries']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', '%'.$search.'%');
        }

        $clients = $query->orderBy('name')->paginate(10)->withQueryString();

        return response()
            ->view('turbo::clients.update', [
                'client' => $client->fresh()->loadCount(['projects', 'timeEntries']),
                'clients' => $clients,
            ])
            ->header('Content-Type', 'text/vnd.turbo-stream.html');
    }
}
