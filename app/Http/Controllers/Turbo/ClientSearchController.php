<?php

namespace App\Http\Controllers\Turbo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientSearchController extends Controller
{
    public function __invoke(Request $request)
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
}
