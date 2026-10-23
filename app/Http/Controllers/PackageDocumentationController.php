<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Package;
use App\Models\Partner;
use App\Models\Transportadora;
use Illuminate\Http\Request;

class PackageDocumentationController extends Controller
{
    public function index(Request $request)
    {
        // Colección de guías sin documentar, de la más antigua a la más reciente.
        $pendingIds = Package::pendingDocumentation()
            ->orderBy('created_at')
            ->orderBy('id')
            ->pluck('id');

        $total = $pendingIds->count();
        $posicion = $total > 0 ? min(max((int) $request->query('posicion', 1), 1), $total) : 0;

        // Desde el buscador se abre una guía concreta, esté pendiente o ya documentada.
        $requestedId = (int) $request->query('guia');
        if ($requestedId > 0 && Package::whereKey($requestedId)->exists()) {
            $index = $pendingIds->search($requestedId);
            $posicion = $index === false ? 0 : $index + 1;
            $selectedId = $requestedId;
        } else {
            $selectedId = $total > 0 ? $pendingIds[$posicion - 1] : null;
        }

        $package = $selectedId
            ? Package::with([
                'photos' => fn ($query) => $query->orderBy('created_at')->orderBy('id'),
                'client',
                'partner',
            ])->find($selectedId)
            : null;

        $clients = Client::orderBy('nombre')->get();
        $partners = Partner::orderBy('nombre')->get();
        $transportadoras = Transportadora::orderBy('nombre')->get();

        return view('documentacion.index', compact('package', 'clients', 'partners', 'transportadoras', 'total', 'posicion'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guia_principal' => ['required', 'string', 'max:255'],
            'guia_secundaria' => ['nullable', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'partner_id' => ['nullable', 'exists:partners,id'],
            'transportadora' => ['nullable', 'string', 'max:255'],
            'guia_master' => ['nullable', 'string', 'max:255'],
            'caja_numero' => ['nullable', 'integer', 'min:1'],
            'total_cajas' => ['nullable', 'integer', 'min:1'],
        ]);

        $package = Package::where('guia_principal', $validated['guia_principal'])->first();

        if (! $package) {
            return back()->withInput()->with('error', 'La guía no existe en el sistema.');
        }

        if (! empty($validated['caja_numero']) && ! empty($validated['total_cajas']) && $validated['caja_numero'] > $validated['total_cajas']) {
            return back()->withInput()->with('error', 'La caja no puede ser mayor que el total de cajas.');
        }

        $client = ! empty($validated['client_id']) ? Client::find($validated['client_id']) : null;
        $partnerId = $client?->partner_id;

        if (! $client) {
            $partnerId = $validated['partner_id'] ?? null;
        }

        $package->markDocumented([
            'guia_secundaria' => $validated['guia_secundaria'] ?? null,
            'client_id' => $validated['client_id'] ?? null,
            'partner_id' => $partnerId,
            'transportadora' => $validated['transportadora'] ?? null,
            'transportadora_id' => ! empty($validated['transportadora'])
                ? Transportadora::where('nombre', $validated['transportadora'])->value('id')
                : null,
            'guia_master' => $validated['guia_master'] ?? null,
            'caja_numero' => $validated['caja_numero'] ?? 1,
            'total_cajas' => $validated['total_cajas'] ?? ($validated['caja_numero'] ?? 1),
        ]);

        // Al salir de la colección la siguiente guía ocupa la misma posición.
        return redirect()->route('documentacion.index', ['posicion' => max((int) $request->input('posicion', 1), 1)])
            ->with('success', 'Documentación actualizada correctamente.');
    }

    public function documented()
    {
        $packages = Package::with(['client', 'partner'])
            ->documented()
            ->latest()
            ->get();

        return view('documentacion.documented', compact('packages'));
    }

    public function markAsReceived(Package $package)
    {
        abort_unless($package->documented_at !== null, 404);

        // Conserva el color previo: amarillo si ya entró a bodega MEX, azul si no.
        $package->markPendingDocumentation();

        return redirect()->route('documentacion.documented')
            ->with('success', 'La guía volvió a pendiente de documentar.');
    }
}
