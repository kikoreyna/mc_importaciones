<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Package;
use App\Models\Partner;
use App\Models\Transportadora;
use Illuminate\Http\Request;

class PackageDocumentationController extends Controller
{
    // Una guía sigue pendiente de documentar hasta que su estado sea "documentado".
    private const PENDING_STATUSES = ['recibido', 'ingreso_bodega'];

    public function index(Request $request)
    {
        $guiaPrincipal = $request->input('guia_principal');

        $package = null;
        $packagesRecibidos = Package::with(['photos', 'client', 'partner'])
            ->whereIn('estado', self::PENDING_STATUSES)
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
        $transportadoras = Transportadora::orderBy('nombre')->get();

        return view('documentacion.index', compact('package', 'clients', 'partners', 'transportadoras', 'guiaPrincipal', 'packagesRecibidos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guia_principal' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'partner_id' => ['nullable', 'exists:partners,id'],
            'transportadora' => ['nullable', 'string', 'max:255'],
            'guia_master' => ['nullable', 'string', 'max:255'],
            'caja_numero' => ['nullable', 'integer', 'min:1'],
            'total_cajas' => ['nullable', 'integer', 'min:1'],
        ]);

        $package = Package::where('guia_principal', $validated['guia_principal'])
            ->whereIn('estado', [...self::PENDING_STATUSES, 'documentado'])
            ->first();

        if (! $package) {
            return back()->withInput()->with('error', 'La guía no está pendiente de documentar o no existe en el sistema.');
        }

        if (! empty($validated['caja_numero']) && ! empty($validated['total_cajas']) && $validated['caja_numero'] > $validated['total_cajas']) {
            return back()->withInput()->with('error', 'La caja no puede ser mayor que el total de cajas.');
        }

        $client = ! empty($validated['client_id']) ? Client::find($validated['client_id']) : null;
        $partnerId = $client?->partner_id;

        if (! $client) {
            $partnerId = $validated['partner_id'] ?? null;
        }

        $package->update([
            'client_id' => $validated['client_id'] ?? null,
            'partner_id' => $partnerId,
            'transportadora' => $validated['transportadora'] ?? null,
            'guia_master' => $validated['guia_master'] ?? null,
            'caja_numero' => $validated['caja_numero'] ?? null,
            'total_cajas' => $validated['total_cajas'] ?? null,
            'estado' => 'documentado',
        ]);

        $nextPackage = Package::whereIn('estado', self::PENDING_STATUSES)
            ->oldest()
            ->first();

        $redirect = $nextPackage
            ? redirect()->route('documentacion.index', ['guia_principal' => $nextPackage->guia_principal])
            : redirect()->route('documentacion.index');

        return $redirect->with('success', 'Documentación actualizada correctamente.');
    }

    public function documented()
    {
        $packages = Package::with(['client', 'partner'])
            ->where('estado', 'documentado')
            ->latest()
            ->get();

        return view('documentacion.documented', compact('packages'));
    }

    public function markAsReceived(Package $package)
    {
        abort_unless($package->estado === 'documentado', 404);

        // Vuelve al estado previo a documentarse; sin historial queda como recibido.
        $previous = $package->logs()
            ->where('action', 'estado_cambiado')
            ->latest('id')
            ->get()
            ->map(fn ($log) => $log->changes['Estado'] ?? null)
            ->first(fn ($change) => is_array($change) && ($change[1] ?? null) === 'documentado')[0] ?? null;

        $package->update([
            'estado' => in_array($previous, self::PENDING_STATUSES, true) ? $previous : 'recibido',
        ]);

        return redirect()->route('documentacion.documented')
            ->with('success', 'La guía volvió a pendiente de documentar.');
    }
}
