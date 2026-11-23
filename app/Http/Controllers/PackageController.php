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

    public function lookup(Request $request)
    {
        $term = trim($request->validate([
            'q' => ['required', 'string', 'max:255'],
        ])['q']);

        $package = Package::query()
            ->where(function ($query) use ($term) {
                $query->where('guia_principal', $term)
                    ->orWhere('guia_secundaria', $term)
                    ->orWhere('guia_master', $term);
            })
            ->first();

        if (! $package) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'package' => [
                'id' => $package->id,
                'guia_principal' => $package->guia_principal,
                'guia_secundaria' => $package->guia_secundaria,
                'guia_master' => $package->guia_master,
            ],
        ]);
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
        ]);

        $this->photoUploadService->handle($request->file('photos', []), $package);

        return redirect()->route('packages.index')->with('success', 'Guía registrada correctamente.');
    }

    public function addPhotos(Request $request, Package $package)
    {
        return $this->storePhotos($request, $package, 'packages.registered');
    }

    public function addPhotosFromScanner(Request $request, Package $package)
    {
        return $this->storePhotos($request, $package, 'packages.index');
    }

    public function addGuideFromScanner(Request $request, Package $package)
    {
        $validated = $request->validate([
            'guide_type' => ['required', Rule::in(['secondary', 'master'])],
            'guide_action' => ['required', Rule::in(['add', 'correct'])],
        ]);
        $field = $validated['guide_type'] === 'secondary' ? 'guia_secundaria' : 'guia_master';
        $currentGuide = $package->{$field};

        if ($validated['guide_action'] === 'add' && $currentGuide) {
            return redirect()->route('packages.index')
                ->withErrors([$field => 'Esta guía ya tiene un registro y no se reemplazó.']);
        }

        if ($validated['guide_action'] === 'correct' && ! $currentGuide) {
            return redirect()->route('packages.index')
                ->withErrors([$field => 'No hay una guía registrada para corregir.']);
        }

        $validatedGuide = $request->validate([
            $field => ['required', 'string', 'max:255'],
        ]);
        $guide = $validatedGuide[$field];
        $alreadyUsed = Package::query()
            ->where(function ($query) use ($guide) {
                $query->where('guia_principal', $guide)
                    ->orWhere('guia_secundaria', $guide)
                    ->orWhere('guia_master', $guide);
            })
            ->whereKeyNot($package->id)
            ->exists();
        $alreadyOnPackage = in_array($guide, [
            $package->guia_principal,
            $field === 'guia_secundaria' ? null : $package->guia_secundaria,
            $field === 'guia_master' ? null : $package->guia_master,
        ], true);

        if ($alreadyUsed || $alreadyOnPackage) {
            return redirect()->route('packages.index')
                ->withErrors([$field => 'Esta guía ya está asociada a otro registro.']);
        }

        $package->update([$field => $guide]);

        $guideType = $field === 'guia_secundaria' ? 'secundaria' : 'máster';
        $message = $validated['guide_action'] === 'correct'
            ? "Guía {$guideType} corregida correctamente."
            : "Guía {$guideType} agregada correctamente.";

        return redirect()->route('packages.index')
            ->with('success', $message);
    }

    protected function storePhotos(Request $request, Package $package, string $redirectRoute)
    {
        $request->validate([
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['file', 'mimetypes:image/jpeg,image/png,image/webp,image/heic,image/heif', 'max:8192'],
        ]);

        $this->photoUploadService->handle($request->file('photos', []), $package);

        return redirect()->route($redirectRoute)->with('success', 'Fotos agregadas correctamente.');
    }

    public function bodegaIndex()
    {
        return view('bodega.index', [
            'pendingCount' => Package::whereNull('received_mex_at')->count(),
        ]);
    }

    public function bodegaStore(Request $request)
    {
        $validated = $request->validate([
            'guia_principal' => ['required', 'string', 'max:255'],
        ]);

        $package = Package::where('guia_principal', $validated['guia_principal'])->first();

        if (! $package) {
            $package = Package::create([
                'guia_principal' => $validated['guia_principal'],
                'received_at' => null,
                'bodega_usa_user_id' => null,
            ]);
            $package->markReceivedInMexico();

            return redirect()->route('bodega.index')
                ->with('warning', 'La guía no fue escaneada en USA. Se registró y se aceptó en bodega MEX.');
        }

        if (! $package->received_mex_at) {
            $package->markReceivedInMexico();
        }

        return redirect()->route('bodega.index')->with('success', 'La guía fue aceptada en bodega.');
    }
}
