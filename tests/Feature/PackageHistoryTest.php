<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PackagePhoto;
use App\Models\User;
use Tests\TestCase;

class PackageHistoryTest extends TestCase
{
    public function test_lifecycle_events_are_logged_with_the_acting_user(): void
    {
        $user = User::factory()->create(['role' => 'bodega_usa', 'name' => 'Ana']);
        $this->actingAs($user);

        $package = Package::create(['guia_principal' => 'G-1', 'estado' => 'recibido']);
        $package->update(['estado' => 'ingreso_bodega']);
        $package->update(['guia_master' => 'M-9']);
        PackagePhoto::create(['package_id' => $package->id, 'file_name' => 'a.jpg', 'path' => 'a.jpg', 'url' => 'a.jpg']);
        $package->delete();

        $this->assertSame(
            ['registrada', 'estado_cambiado', 'actualizada', 'foto_agregada', 'eliminada'],
            $package->logs()->orderBy('id')->pluck('action')->all(),
        );
        $this->assertSame(1, $package->logs()->where('action', 'estado_cambiado')->whereJsonContains('changes->Estado', 'ingreso_bodega')->count());
        $this->assertSame(5, $package->logs()->where('user_id', $user->id)->where('user_name', 'Ana')->count());
    }

    public function test_history_is_visible_to_any_role_including_deleted_packages(): void
    {
        $package = Package::create(['guia_principal' => 'G-2', 'estado' => 'recibido']);
        $package->delete();

        foreach (['administrador', 'supervisor', 'documentador', 'bodega_usa', 'bodega_mex'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('packages.history', $package))
                ->assertOk()
                ->assertSee('Guía registrada')
                ->assertSee('Guía eliminada');
        }
    }
}
