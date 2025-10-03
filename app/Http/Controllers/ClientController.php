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
        $query = Client::withCount(['projects', 'timeEntries']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', '%'.$search.'%');
        }

        $clients = $query->orderBy('name')->paginate(10)->withQueryString();

        redirect()->redirectIfLastPageEmpty($request, $clients);

        return view('clients.index', ['clients' => $clients]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|min:3',
                'hourly_rate_amount' => 'nullable|numeric|min:0',
                'hourly_rate_currency' => 'required_with:hourly_rate_amount|string|in:'.implode(',', array_column(\App\Enums\Currency::cases(), 'value')),
            ]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception->redirectTo(route('turbo.clients.create'));
        }

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

        InAppNotification::success(__('New client successfully created.'));

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

    public function update(Request $request, Client $client)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'hourly_rate_amount' => 'nullable|numeric|min:0',
                'hourly_rate_currency' => 'required_with:hourly_rate_amount|string|in:'.implode(',', array_column(\App\Enums\Currency::cases(), 'value')),
            ]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception->redirectTo(route('turbo.clients.edit', $client));
        }

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

        InAppNotification::success(__('Client successfully updated.'));

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

    public function destroy(Client $client)
    {
        $client->delete();

        return to_intended_route('clients.index');
    }
}
