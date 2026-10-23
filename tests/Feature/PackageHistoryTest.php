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

        $package = Package::create(['guia_principal' => 'G-1']);
        $package->markReceivedInMexico();
        $package->update(['guia_master' => 'M-9']);
        PackagePhoto::create(['package_id' => $package->id, 'file_name' => 'a.jpg', 'path' => 'a.jpg', 'url' => 'a.jpg']);
        $package->delete();

        $this->assertSame(
            ['registrada', 'color_cambiado', 'actualizada', 'foto_agregada', 'eliminada'],
            $package->logs()->orderBy('id')->pluck('action')->all(),
        );
        $this->assertSame(1, $package->logs()->where('action', 'color_cambiado')->whereJsonContains('changes->Color', 'amarillo')->count());
        $this->assertSame(5, $package->logs()->where('user_id', $user->id)->where('user_name', 'Ana')->count());
    }

    public function test_history_is_visible_only_to_management_roles(): void
    {
        $package = Package::create(['guia_principal' => 'G-2']);
        $package->delete();

        foreach (['administrador', 'supervisor'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('packages.history', $package))
                ->assertOk()
                ->assertSee('Guía registrada')
                ->assertSee('Guía eliminada');
        }

        foreach (['documentador', 'bodega_usa', 'bodega_mex'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('packages.history', $package))
                ->assertForbidden();
        }
    }
}
