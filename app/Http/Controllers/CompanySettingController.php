<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class CompanySettingController extends Controller
{
    public function edit()
    {
        $company = CompanySetting::query()->find(1) ?? new CompanySetting(['name' => '']);

        return view('company-settings.edit', compact('company'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
            'logo_png' => ['nullable', 'image', 'mimes:png', 'max:4096', 'dimensions:max_width=512,max_height=512'],
            'favicon_png' => ['nullable', 'image', 'mimes:png', 'max:512', 'dimensions:max_width=64,max_height=64'],
        ]);

        if ($request->hasFile('logo_png') !== $request->hasFile('favicon_png')) {
            throw ValidationException::withMessages([
                'logo_png' => 'Selecciona nuevamente el logo para generar también su favicon.',
            ]);
        }

        $company = CompanySetting::query()->find(1) ?? new CompanySetting(['id' => 1]);
        $previousPaths = [$company->logo_path, $company->favicon_path];
        $newPaths = [];

        try {
            if ($request->hasFile('logo_png')) {
                $data['logo_path'] = $this->storePng($request->file('logo_png'), 'logo');
                $newPaths[] = $data['logo_path'];
                $data['favicon_path'] = $this->storePng($request->file('favicon_png'), 'favicon');
                $newPaths[] = $data['favicon_path'];
            }

            unset($data['logo_png'], $data['favicon_png']);
            $company->fill($data)->save();
        } catch (Throwable $exception) {
            Storage::disk('public')->delete(array_filter($newPaths));
            throw $exception;
        }

        Storage::disk('public')->delete(array_filter(array_diff($previousPaths, $newPaths)));

        return redirect()->route('company-settings.edit')->with('success', 'Los datos de la empresa se guardaron correctamente.');
    }

    public function logo()
    {
        return $this->imageResponse(CompanySetting::query()->find(1)?->logo_path);
    }

    public function favicon()
    {
        return $this->imageResponse(CompanySetting::query()->find(1)?->favicon_path);
    }

    private function storePng(UploadedFile $file, string $kind): string
    {
        $path = $file->storePubliclyAs('company', $kind.'-'.Str::uuid().'.png', 'public');

        if (! $path) {
            throw ValidationException::withMessages([
                'logo_png' => 'No se pudo guardar la imagen. Inténtalo de nuevo.',
            ]);
        }

        return $path;
    }

    private function imageResponse(?string $path)
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return response()->noContent();
        }

        return response()->file(Storage::disk('public')->path($path), [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}