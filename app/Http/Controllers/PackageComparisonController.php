<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackagePhoto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class PackageComparisonController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'fecha' => ['nullable', 'date'],
            'grupo' => ['nullable', Rule::in(['usa', 'mex', 'pendientes'])],
        ]);

        $fecha = $validated['fecha'] ?? today()->toDateString();
        $grupo = $validated['grupo'] ?? null;
        $baseQuery = Package::query()->when($fecha, fn ($query) => $query->whereDate('created_at', $fecha));
        $pendingQuery = Package::query()->whereNull('received_mex_at');
        $receivedInUsa = (clone $baseQuery)->count();
        $receivedInMexico = (clone $baseQuery)->receivedInMexico()->count();
        $pendingInMexico = (clone $pendingQuery)->count();
        $totalPackages = (clone $baseQuery)->sum('total_paquetes');
        $arrivedPackages = (clone $baseQuery)
            ->receivedInMexico()
            ->sum('total_paquetes');
        $packagesQuery = $grupo === 'pendientes'
            ? clone $pendingQuery
            : clone $baseQuery;
        if ($grupo === 'mex') {
            $packagesQuery->receivedInMexico();
        }

        $packages = $packagesQuery->latest()->get();
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
            'grupo',
            'packages',
        ));
    }

    public function edit(Request $request, Package $package)
    {
        return view('reportes.editar-guia', [
            'package' => $package,
            'returnTo' => $request->string('return_to')->toString() === 'reporteria' ? 'reporteria' : 'comparativo',
        ]);
    }

    public function destroy(Request $request, Package $package)
    {
        $disk = config('filesystems.disks.s3.bucket') ? 's3' : 'local';

        foreach ($package->photos as $photo) {
            if ($photo->path) {
                Storage::disk($disk)->delete($photo->path);
            }

            $photo->delete();
        }

        $package->delete();

        $route = $request->string('return_to')->toString() === 'reporteria'
            ? 'reports.packages'
            : 'reports.comparison';

        return redirect()->route($route)->with('success', 'La guía fue eliminada correctamente.');
    }

    public function destroyPhoto(PackagePhoto $photo)
    {
        if ($photo->path) {
            $disk = config('filesystems.disks.s3.bucket') ? 's3' : 'local';
            Storage::disk($disk)->delete($photo->path);
        }

        $photo->delete();

        return back()->with('success', 'La fotografía fue eliminada correctamente.');
    }
}