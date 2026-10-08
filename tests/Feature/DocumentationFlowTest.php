<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Tests\TestCase;

class DocumentationFlowTest extends TestCase
{
    public function test_guide_received_in_bodega_mex_stays_pending_until_documented(): void
    {
        $package = Package::create(['guia_principal' => 'MEX-1', 'estado' => 'recibido']);
        $this->actingAs(User::factory()->create(['role' => 'bodega_mex']))
            ->post(route('bodega.store'), ['guia_principal' => 'MEX-1'])
            ->assertRedirect();

        $this->assertSame('ingreso_bodega', $package->fresh()->estado);

        $documentador = User::factory()->create(['role' => 'documentador']);

        $this->actingAs($documentador)
            ->get(route('documentacion.index'))
            ->assertOk()
            ->assertSee('MEX-1');

        $this->actingAs($documentador)
            ->post(route('documentacion.store'), ['guia_principal' => 'MEX-1'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('documentado', $package->fresh()->estado);

        $this->actingAs($documentador)
            ->get(route('documentacion.index'))
            ->assertOk()
            ->assertDontSee('MEX-1');
    }

    public function test_returning_a_documented_guide_restores_its_previous_pending_state(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'documentador']));

        $fromMexico = Package::create(['guia_principal' => 'BACK-1', 'estado' => 'ingreso_bodega']);
        $fromMexico->update(['estado' => 'documentado']);
        $fromUsa = Package::create(['guia_principal' => 'BACK-2', 'estado' => 'recibido']);
        $fromUsa->update(['estado' => 'documentado']);

        $this->patch(route('documentacion.markAsReceived', $fromMexico))->assertRedirect();
        $this->patch(route('documentacion.markAsReceived', $fromUsa))->assertRedirect();

        $this->assertSame('ingreso_bodega', $fromMexico->fresh()->estado);
        $this->assertSame('recibido', $fromUsa->fresh()->estado);
    }

    public function test_bodega_does_not_revert_a_documented_guide(): void
    {
        $package = Package::create(['guia_principal' => 'DOC-1', 'estado' => 'documentado']);

        $this->actingAs(User::factory()->create(['role' => 'bodega_mex']))
            ->post(route('bodega.store'), ['guia_principal' => 'DOC-1'])
            ->assertRedirect();

        $this->assertSame('documentado', $package->fresh()->estado);
    }
}
