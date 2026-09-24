<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

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
        ));
    }
}