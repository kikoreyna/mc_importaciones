<?php

namespace App\Http\Controllers;

use App\Models\Transportadora;
use Illuminate\Http\Request;

class TransportadoraController extends Controller
{
    public function index()
    {
        $transportadoras = Transportadora::latest()->get();

        return view('transportadoras.index', compact('transportadoras'));
    }

    public function create()
    {
        return view('transportadoras.form', ['transportadora' => new Transportadora()]);
    }

    public function store(Request $request)
    {
        Transportadora::create($this->validated($request));

        return redirect()->route('transportadoras.index')->with('success', 'Transportadora creada correctamente.');
    }

    public function edit(Transportadora $transportadora)
    {
        return view('transportadoras.form', compact('transportadora'));
    }

    public function update(Request $request, Transportadora $transportadora)
    {
        $transportadora->update($this->validated($request));

        return redirect()->route('transportadoras.index')->with('success', 'Transportadora actualizada correctamente.');
    }

    public function destroy(Transportadora $transportadora)
    {
        $transportadora->delete();

        return redirect()->route('transportadoras.index')->with('success', 'Transportadora eliminada correctamente.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'web' => ['required', 'url', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
