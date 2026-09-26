<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Package;
use App\Models\Transportadora;
use Illuminate\Http\Request;

class PackageReportController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'guia_principal' => ['nullable', 'string', 'max:255'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'transportadora' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'max:255'],
        ]);

        $packages = Package::with(['client', 'partner'])
            ->when($validated['guia_principal'] ?? null, fn ($query, $guide) => $query->where('guia_principal', 'like', "%{$guide}%"))
            ->when($validated['client_id'] ?? null, fn ($query, $clientId) => $query->where('client_id', $clientId))
            ->when($validated['transportadora'] ?? null, fn ($query, $carrier) => $query->where('transportadora', $carrier))
            ->when($validated['estado'] ?? null, fn ($query, $status) => $query->where('estado', $status))
            ->latest()
            ->get();

        $clients = Client::orderBy('nombre')->get();
        $transportadoras = Package::query()
            ->whereNotNull('transportadora')
            ->where('transportadora', '!=', '')
            ->distinct()
            ->orderBy('transportadora')
            ->pluck('transportadora');
        $transportadoraWebs = Transportadora::query()
            ->whereIn('nombre', $transportadoras)
            ->pluck('web', 'nombre');
        $estados = Package::query()
            ->whereNotNull('estado')
            ->distinct()
            ->orderBy('estado')
            ->pluck('estado');

        return view('reportes.packages', compact('packages', 'clients', 'transportadoras', 'transportadoraWebs', 'estados'));
    }
}
