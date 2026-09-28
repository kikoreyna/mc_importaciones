<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Services\PackagePhotoUploadService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    public function __construct(protected PackagePhotoUploadService $photoUploadService)
    {
    }

    public function index()
    {
        return view('packages.index');
    }

    public function registeredIndex()
    {
        $packages = Package::with('photos')
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        return view('packages.registered', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guia_principal' => ['required', 'string', 'max:255', Rule::unique('packages', 'guia_principal')],
            'guia_secundaria' => ['nullable', 'string', 'max:255'],
            'guia_master' => ['nullable', 'string', 'max:255'],
            'total_paquetes' => ['nullable', 'integer', 'min:1'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['file', 'mimetypes:image/jpeg,image/png,image/webp,image/heic,image/heif', 'max:8192'],
        ]);

        $package = Package::create([
            'guia_principal' => $validated['guia_principal'],
            'guia_secundaria' => $validated['guia_secundaria'] ?? null,
            'guia_master' => $validated['guia_master'] ?? null,
            'total_paquetes' => $validated['total_paquetes'] ?? 1,
            'estado' => 'recibido',
        ]);

        $this->photoUploadService->handle($request->file('photos', []), $package);

        return redirect()->route('packages.index')->with('success', 'Guía registrada correctamente.');
    }

    public function bodegaIndex()
    {
        return view('bodega.index');
    }

    public function bodegaStore(Request $request)
    {
        $validated = $request->validate([
            'guia_principal' => ['required', 'string', 'max:255'],
        ]);

        $package = Package::where('guia_principal', $validated['guia_principal'])->first();

        if (! $package) {
            return back()->with('error', 'La guía no existe en el sistema. Debe registrarse primero en USA.');
        }

        $package->update(['estado' => 'ingreso_bodega']);

        return redirect()->route('bodega.index')->with('success', 'La guía fue aceptada en bodega.');
    }
}
