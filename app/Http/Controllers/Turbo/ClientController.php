<?php

namespace App\Http\Controllers\Turbo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Turbo\StoreClientRequest;
use App\Http\Requests\Turbo\UpdateClientRequest;
use App\Models\Client;
use App\ValueObjects\Money;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function create(): View
    {
        return view('turbo::clients.create');
    }

    public function store(StoreClientRequest $request): JsonResponse|Response
    {
        $validated = $request->validated();

        $hourlyRate = null;
        if (! empty($validated['hourly_rate_amount'])) {
            $hourlyRate = Money::fromDecimal(
                amount: (float) $validated['hourly_rate_amount'],
                currency: $validated['hourly_rate_currency'] ?? 'USD'
            );
        }

        $client = Client::create([
            'name' => $validated['name'],
            'hourly_rate' => $hourlyRate,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return new JsonResponse([
                'success' => true,
                'client' => [
                    'id' => $client->id,
                    'name' => $client->name,
                    'hourly_rate' => $client->hourly_rate ? $client->hourly_rate->formatted() : null,
                ],
            ]);
        }

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

    public function edit(Client $client): View
    {
        return view('turbo::clients.edit', ['client' => $client]);
    }

    public function update(UpdateClientRequest $request, Client $client): Response
    {
        $validated = $request->validated();

        $hourlyRate = null;
        if (! empty($validated['hourly_rate_amount'])) {
            $hourlyRate = Money::fromDecimal(
                amount: (float) $validated['hourly_rate_amount'],
                currency: $validated['hourly_rate_currency'] ?? 'USD'
            );
        }

        $client->update([
            'name' => $validated['name'],
            'hourly_rate' => $hourlyRate,
        ]);

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
