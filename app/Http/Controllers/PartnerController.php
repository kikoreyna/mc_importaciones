<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::withCount('clients')->latest()->get();

        return view('partners.index', compact('partners'));
    }

    public function create()
    {
        return view('partners.form', ['partner' => new Partner()]);
    }

    public function store(Request $request)
    {
        Partner::create($this->validated($request));

        return redirect()->route('partners.index')->with('success', 'Socio creado correctamente.');
    }

    public function edit(Partner $partner)
    {
        return view('partners.form', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $partner->update($this->validated($request));

        return redirect()->route('partners.index')->with('success', 'Socio actualizado correctamente.');
    }

    public function destroy(Partner $partner)
    {
        if ($partner->clients()->exists()) {
            return back()->with('error', 'No puedes eliminar un socio que tiene clientes activos.');
        }

        $partner->delete();

        return redirect()->route('partners.index')->with('success', 'Socio eliminado correctamente.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'alias' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
