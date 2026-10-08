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

    public function search(Request $request)
    {
        $term = trim($request->validate(['q' => ['nullable', 'string', 'max:255']])['q'] ?? '');

        $packages = $term === ''
            ? collect()
            : Package::with(['photos', 'client', 'partner'])
                ->where(function ($query) use ($term) {
                    $like = '%' . addcslashes($term, '%_\\') . '%';
                    $query->where('guia_principal', 'like', $like)
                        ->orWhere('guia_secundaria', 'like', $like)
                        ->orWhere('guia_master', 'like', $like);
                })
                ->latest()
                ->limit(50)
                ->get();

        return view('packages.search', compact('packages', 'term'));
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

    public function addPhotos(Request $request, Package $package)
    {
        $request->validate([
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['file', 'mimetypes:image/jpeg,image/png,image/webp,image/heic,image/heif', 'max:8192'],
        ]);

        $this->photoUploadService->handle($request->file('photos', []), $package);

        return redirect()->route('packages.registered')->with('success', 'Fotos agregadas correctamente.');
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

        if ($package->estado === 'documentado') {
            return redirect()->route('bodega.index')->with('success', 'La guía ya está documentada; se mantiene su estado.');
        }

        $package->update(['estado' => 'ingreso_bodega']);

        return redirect()->route('bodega.index')->with('success', 'La guía fue aceptada en bodega.');
    }
}
