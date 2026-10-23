<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PackagePhoto;
use App\Models\User;
use Tests\TestCase;

class DocumentationFlowTest extends TestCase
{
    public function test_guide_received_in_bodega_mex_stays_pending_until_documented(): void
    {
        $package = Package::create(['guia_principal' => 'MEX-1']);
        $bodegaMex = User::factory()->create(['role' => 'bodega_mex']);
        $this->actingAs($bodegaMex)
            ->post(route('bodega.store'), ['guia_principal' => 'MEX-1'])
            ->assertRedirect();

        $this->assertSame('amarillo', $package->fresh()->color);
        $this->assertSame($bodegaMex->id, $package->fresh()->bodega_mex_user_id);

        $documentador = User::factory()->create(['role' => 'documentador']);

        $this->actingAs($documentador)
            ->get(route('documentacion.index'))
            ->assertOk()
            ->assertSee('MEX-1')
            ->assertSee('Actualizar Guia')
            ->assertSee('Este paquete no tiene fotos disponibles.')
            ->assertDontSee('Buscar guía')
            ->assertDontSee('Ver paquetes documentados')
            ->assertDontSee('Pendientes');

        $this->actingAs($documentador)
            ->post(route('documentacion.store'), ['guia_principal' => 'MEX-1'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('verde', $package->fresh()->color);
        $this->assertSame($documentador->id, $package->fresh()->documentador_user_id);
    }

    public function test_returning_a_documented_guide_restores_its_previous_pending_state(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'documentador']));

        $fromMexico = Package::create(['guia_principal' => 'BACK-1', 'received_mex_at' => now()]);
        $fromMexico->markDocumented();
        $fromUsa = Package::create(['guia_principal' => 'BACK-2']);
        $fromUsa->markDocumented();

        $this->patch(route('documentacion.markAsReceived', $fromMexico))->assertRedirect();
        $this->patch(route('documentacion.markAsReceived', $fromUsa))->assertRedirect();

        $this->assertSame('amarillo', $fromMexico->fresh()->color);
        $this->assertSame('azul', $fromUsa->fresh()->color);
    }

    public function test_documentation_photos_are_shown_oldest_first_without_a_counter(): void
    {
        $package = Package::create(['guia_principal' => 'MEX-PHOTOS']);
        $newestPhoto = PackagePhoto::create([
            'package_id' => $package->id,
            'file_name' => 'newest.jpg',
            'path' => 'newest.jpg',
            'url' => 'https://example.test/newest.jpg',
        ]);
        $oldestPhoto = PackagePhoto::create([
            'package_id' => $package->id,
            'file_name' => 'oldest.jpg',
            'path' => 'oldest.jpg',
            'url' => 'https://example.test/oldest.jpg',
        ]);
        $newestPhoto->created_at = now();
        $newestPhoto->save();
        $oldestPhoto->created_at = now()->subDay();
        $oldestPhoto->save();

        $this->actingAs(User::factory()->create(['role' => 'documentador']))
            ->get(route('documentacion.index'))
            ->assertOk()
            ->assertDontSee('photo-counter')
            ->assertDontSee('por documentar')
            ->assertSee('id="prev-photo"', false)
            ->assertSee('id="next-photo"', false)
            ->assertSeeInOrder(['oldest.jpg', 'newest.jpg']);
    }

    public function test_pending_guides_are_navigable_oldest_first_with_counters(): void
    {
        $newest = Package::create(['guia_principal' => 'NAV-3']);
        $middle = Package::create(['guia_principal' => 'NAV-2', 'received_mex_at' => now()]);
        $oldest = Package::create(['guia_principal' => 'NAV-1']);
        Package::create(['guia_principal' => 'NAV-DONE', 'documented_at' => now()]);
        $newest->forceFill(['created_at' => now()])->saveQuietly();
        $middle->forceFill(['created_at' => now()->subDay()])->saveQuietly();
        $oldest->forceFill(['created_at' => now()->subDays(2)])->saveQuietly();

        $this->actingAs(User::factory()->create(['role' => 'documentador']));

        $this->get(route('documentacion.index'))
            ->assertOk()
            ->assertSee('1/3 sin documentar')
            ->assertSee('value="NAV-1"', false)
            ->assertSee('id="next-package"', false)
            ->assertDontSee('id="previous-package"', false);

        $this->get(route('documentacion.index', ['posicion' => 3]))
            ->assertSee('3/3 sin documentar')
            ->assertSee('value="NAV-3"', false)
            ->assertSee('id="previous-package"', false)
            ->assertDontSee('id="next-package"', false);

        $this->get(route('documentacion.index', ['posicion' => 99]))->assertSee('3/3 sin documentar');

        $this->post(route('documentacion.store'), ['guia_principal' => 'NAV-1', 'posicion' => 1])
            ->assertRedirect(route('documentacion.index', ['posicion' => 1]));

        $this->get(route('documentacion.index'))
            ->assertSee('1/2 sin documentar')
            ->assertSee('value="NAV-2"', false);
    }

    public function test_search_result_opens_the_documentation_edit_view_for_that_guide(): void
    {
        $done = Package::create(['guia_principal' => 'FIND-DONE', 'documented_at' => now()]);
        Package::create(['guia_principal' => 'FIND-PENDING']);

        $this->actingAs(User::factory()->create(['role' => 'supervisor']))
            ->get(route('packages.search', ['q' => 'FIND-DONE']))
            ->assertOk()
            ->assertSee(route('documentacion.index', ['guia' => $done->id]), false);

        $this->get(route('documentacion.index', ['guia' => $done->id]))
            ->assertOk()
            ->assertSee('value="FIND-DONE"', false)
            ->assertSee('Documentada');
    }

    public function test_bodega_does_not_revert_a_documented_guide(): void
    {
        $package = Package::create(['guia_principal' => 'DOC-1', 'documented_at' => now()]);

        $this->actingAs(User::factory()->create(['role' => 'bodega_mex']))
            ->post(route('bodega.store'), ['guia_principal' => 'DOC-1'])
            ->assertRedirect();

        $this->assertSame('verde', $package->fresh()->color);
        $this->assertNull($package->fresh()->received_mex_at);
    }
}
