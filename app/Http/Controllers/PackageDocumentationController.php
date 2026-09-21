<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Package;
use App\Models\Partner;
use Illuminate\Http\Request;

class PackageDocumentationController extends Controller
{
    public function index(Request $request)
    {
        $guiaPrincipal = $request->input('guia_principal');

        $package = null;
        $packagesRecibidos = Package::with(['photos', 'client', 'partner'])
            ->where('estado', 'recibido')
            ->oldest()
            ->get();

        if ($guiaPrincipal) {
            $package = Package::with(['photos', 'client', 'partner'])
                ->where('guia_principal', $guiaPrincipal)
                ->first();
        } elseif ($packagesRecibidos->isNotEmpty()) {
            $package = $packagesRecibidos->first();
        }

        if ($package) {
            $packagesRecibidos = $packagesRecibidos
                ->reject(fn ($item) => $item->id === $package->id)
                ->values();
        }

        $clients = Client::orderBy('nombre')->get();
        $partners = Partner::orderBy('nombre')->get();
        $transportadoras = ['DHL', 'FedEx', 'UPS', 'USPS', 'Amazon Logistics', 'Otra'];

        return view('documentacion.index', compact('package', 'clients', 'partners', 'transportadoras', 'guiaPrincipal', 'packagesRecibidos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guia_principal' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'partner_id' => ['nullable', 'exists:partners,id'],
            'transportadora' => ['nullable', 'string', 'max:255'],
            'caja_numero' => ['nullable', 'integer', 'min:1'],
            'total_cajas' => ['nullable', 'integer', 'min:1'],
        ]);

        $package = Package::where('guia_principal', $validated['guia_principal'])
            ->where('estado', 'recibido')
            ->first();

        if (! $package) {
            return back()->withInput()->with('error', 'La guía no está en estado recibido o no existe en el sistema.');
        }

        if (! empty($validated['caja_numero']) && ! empty($validated['total_cajas']) && $validated['caja_numero'] > $validated['total_cajas']) {
            return back()->withInput()->with('error', 'La caja no puede ser mayor que el total de cajas.');
        }

        $client = $validated['client_id'] ? Client::find($validated['client_id']) : null;
        $partnerId = $client?->partner_id;

        if (! $client) {
            $partnerId = $validated['partner_id'] ?? null;
        }

        $package->update([
            'client_id' => $validated['client_id'] ?? null,
            'partner_id' => $partnerId,
            'transportadora' => $validated['transportadora'] ?? null,
            'caja_numero' => $validated['caja_numero'] ?? null,
            'total_cajas' => $validated['total_cajas'] ?? null,
            'estado' => 'documentado',
        ]);

        return redirect()->route('documentacion.index', ['guia_principal' => $package->guia_principal])
            ->with('success', 'Documentación actualizada correctamente.');
    }
}
