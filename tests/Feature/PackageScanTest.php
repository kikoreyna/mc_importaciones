<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PackageScanTest extends TestCase
{
    public function test_can_store_a_main_guide_with_an_optional_secondary_guide_and_photos(): void
    {
        Storage::fake('local');

        $response = $this->post('/packages', [
            'guia_principal' => 'USA-123',
            'guia_secundaria' => 'SEC-456',
            'guia_master' => 'MASTER-001',
            'total_paquetes' => 3,
            'photos' => [
                UploadedFile::fake()->create('photo-1.jpg', 1024, 'image/jpeg'),
                UploadedFile::fake()->create('photo-2.png', 1024, 'image/png'),
            ],
        ]);

        $response->assertRedirect('/packages');

        $this->assertDatabaseHas('packages', [
            'guia_principal' => 'USA-123',
            'guia_secundaria' => 'SEC-456',
            'guia_master' => 'MASTER-001',
            'total_paquetes' => 3,
        ]);

        $package = Package::query()->firstOrFail();
        $this->assertCount(2, $package->photos);
    }

    public function test_can_show_the_package_scanner_page(): void
    {
        $response = $this->get('/packages');

        $response->assertOk();
    }

    public function test_package_starts_as_received_after_scan(): void
    {
        $response = $this->post('/packages', [
            'guia_principal' => 'USA-200',
        ]);

        $response->assertRedirect('/packages');
        $this->assertDatabaseHas('packages', [
            'guia_principal' => 'USA-200',
            'estado' => 'recibido',
        ]);
    }

    public function test_cannot_register_a_duplicate_main_guide(): void
    {
        Package::create([
            'guia_principal' => 'USA-DUPLICADA',
            'estado' => 'recibido',
        ]);

        $response = $this->from('/packages')->post('/packages', [
            'guia_principal' => 'USA-DUPLICADA',
        ]);

        $response->assertRedirect('/packages');
        $response->assertSessionHasErrors('guia_principal');
        $this->assertSame(1, Package::where('guia_principal', 'USA-DUPLICADA')->count());
    }

    public function test_bodega_rejects_a_guide_that_does_not_exist(): void
    {
        $response = $this->post('/bodega', [
            'guia_principal' => 'NO-EXISTE-999',
        ]);

        $response->assertSessionHas('error', 'La guía no existe en el sistema. Debe registrarse primero en USA.');
    }

    public function test_comparison_shows_usa_and_mexico_receipts(): void
    {
        Package::create([
            'guia_principal' => 'USA-PENDIENTE',
            'total_paquetes' => 2,
            'estado' => 'recibido',
        ]);
        Package::create([
            'guia_principal' => 'USA-EN-MEXICO',
            'total_paquetes' => 3,
            'estado' => 'ingreso_bodega',
        ]);

        $response = $this->get('/reportes/comparativo');

        $response->assertOk();
        $response->assertSee('USA-PENDIENTE');
        $response->assertSee('Recibidas en USA');
        $response->assertSee('Recibidas en Bodega MEX');
        $response->assertSee('50%');

        $pendingResponse = $this->get('/reportes/comparativo?fecha=' . today()->toDateString() . '&grupo=pendientes');

        $pendingResponse->assertOk();
        $pendingResponse->assertSee('USA-PENDIENTE');
        $pendingResponse->assertDontSee('USA-EN-MEXICO');
    }

    public function test_only_managers_can_update_and_delete_comparison_guides(): void
    {
        $package = Package::create([
            'guia_principal' => 'USA-ADMINISTRABLE',
            'estado' => 'recibido',
        ]);
        $supervisor = User::factory()->create(['role' => 'supervisor']);

        $this->actingAs($supervisor)
            ->patch(route('reports.comparison.update', $package), ['estado' => 'devuelto_usa'])
            ->assertRedirect();

        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'estado' => 'devuelto_usa',
        ]);

        $operator = User::factory()->create(['role' => 'operador']);

        $this->actingAs($operator)
            ->patch(route('reports.comparison.update', $package), ['estado' => 'cancelado'])
            ->assertForbidden();

        $this->actingAs($supervisor)
            ->delete(route('reports.comparison.destroy', $package))
            ->assertRedirect();

        $this->assertDatabaseMissing('packages', ['id' => $package->id]);
    }
}
