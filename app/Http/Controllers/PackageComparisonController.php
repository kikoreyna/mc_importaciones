<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PackageComparisonController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'fecha' => ['nullable', 'date'],
        ]);

        $fecha = $validated['fecha'] ?? null;
        $baseQuery = Package::query()->when($fecha, fn ($query) => $query->whereDate('created_at', $fecha));
        $receivedInUsa = (clone $baseQuery)->count();
        $receivedInMexico = (clone $baseQuery)->whereIn('estado', ['ingreso_bodega', 'documentado'])->count();
        $pendingInMexico = (clone $baseQuery)->where('estado', 'recibido')->count();
        $totalPackages = (clone $baseQuery)->sum('total_paquetes');
        $arrivedPackages = (clone $baseQuery)
            ->whereIn('estado', ['ingreso_bodega', 'documentado'])
            ->sum('total_paquetes');
        $pendingGuides = (clone $baseQuery)
            ->where('estado', 'recibido')
            ->latest()
            ->get(['guia_principal', 'guia_secundaria', 'total_paquetes', 'created_at']);
        $packages = (clone $baseQuery)->latest()->get();
        $statuses = [
            'recibido' => 'Recibido en USA',
            'ingreso_bodega' => 'Recibido en Bodega MEX',
            'documentado' => 'Documentado',
            'devuelto_cliente' => 'Devuelto a cliente',
            'devuelto_usa' => 'Devuelto a USA',
            'cancelado' => 'Cancelado',
        ];

        $arrivalPercentage = $receivedInUsa > 0
            ? round(($receivedInMexico / $receivedInUsa) * 100)
            : 0;

        return view('reportes.comparativo', compact(
            'fecha',
            'receivedInUsa',
            'receivedInMexico',
            'pendingInMexico',
            'totalPackages',
            'arrivedPackages',
            'arrivalPercentage',
            'pendingGuides',
            'packages',
            'statuses',
        ));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'estado' => ['required', Rule::in(['recibido', 'ingreso_bodega', 'documentado', 'devuelto_cliente', 'devuelto_usa', 'cancelado'])],
        ]);

        $package->update(['estado' => $validated['estado']]);

        return back()->with('success', 'El estado de la guía fue actualizado correctamente.');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return back()->with('success', 'La guía fue eliminada correctamente.');
    }
}