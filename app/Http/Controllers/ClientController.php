<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with('partner')->latest()->get();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.form', [
            'client' => new Client(),
            'partners' => Partner::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Client::create($this->validated($request));

        return redirect()->route('clients.index')->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Client $client)
    {
        return view('clients.form', [
            'client' => $client,
            'partners' => Partner::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $client->update($this->validated($request, $client));

        return redirect()->route('clients.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Cliente eliminado correctamente.');
    }

    private function validated(Request $request, ?Client $client = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'alias' => ['nullable', 'string', 'max:255'],
            'partner_id' => [
                'nullable',
                Rule::exists('partners', 'id')->whereNull('deleted_at'),
            ],
        ]);
    }
}
