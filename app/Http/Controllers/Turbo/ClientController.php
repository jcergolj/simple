<?php

namespace App\Http\Controllers\Turbo;

use App\Http\Controllers\Controller;
use App\Models\Client;

class ClientController extends Controller
{
    public function create()
    {
        return view('turbo::clients.create');
    }

    public function edit(Client $client)
    {
        return view('turbo::clients.edit', ['client' => $client]);
    }
}
