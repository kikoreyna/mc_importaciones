<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form', ['user' => new User()]);
    }

    public function store(Request $request)
    {
        User::create($this->validated($request));

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);

        if ($user->role === 'administrador' && $data['role'] !== 'administrador' && User::where('role', 'administrador')->count() === 1) {
            return back()->with('error', 'Debe existir al menos un administrador.');
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'administrador' && User::where('role', 'administrador')->count() === 1) {
            return back()->with('error', 'No puedes eliminar al último administrador.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)->whereNull('deleted_at')],
            'role' => ['required', Rule::in(['administrador', 'supervisor', 'documentador', 'bodega_usa', 'bodega_mex'])],
            'password' => [$user?->exists ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ];

        $data = $request->validate($rules);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }
}
