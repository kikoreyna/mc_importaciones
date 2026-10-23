<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PackageScanTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'administrador']));
    }

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
        $package = Package::where('guia_principal', 'USA-200')->firstOrFail();
        $this->assertSame('azul', $package->color);
        $this->assertNotNull($package->registered_at);
        $this->assertNotNull($package->received_at);
    }

    public function test_cannot_register_a_duplicate_main_guide(): void
    {
        Package::create([
            'guia_principal' => 'USA-DUPLICADA',
        ]);

        $response = $this->from('/packages')->post('/packages', [
            'guia_principal' => 'USA-DUPLICADA',
        ]);

        $response->assertRedirect('/packages');
        $response->assertSessionHasErrors('guia_principal');
        $this->assertSame(1, Package::where('guia_principal', 'USA-DUPLICADA')->count());
    }

    public function test_bodega_registers_a_guide_missing_in_usa_with_a_warning(): void
    {
        $response = $this->post('/bodega', [
            'guia_principal' => 'NO-EXISTE-999',
        ]);

        $response->assertSessionHas('warning');
        $package = Package::where('guia_principal', 'NO-EXISTE-999')->firstOrFail();
        $this->assertNull($package->received_at);
        $this->assertNotNull($package->received_mex_at);
        $this->assertSame('amarillo', $package->color);
    }

    public function test_bodega_counter_drops_as_guides_are_scanned(): void
    {
        Package::create(['guia_principal' => 'PEND-1']);
        Package::create(['guia_principal' => 'PEND-2']);

        $this->get('/bodega')->assertOk()->assertSee('id="pending-counter"', false)->assertSeeInOrder(['Recepción', '>2<']);

        $this->post('/bodega', ['guia_principal' => 'PEND-1']);

        $this->get('/bodega')->assertSeeInOrder(['Recepción', '>1<']);
    }

    public function test_comparison_shows_usa_and_mexico_receipts(): void
    {
        Package::create([
            'guia_principal' => 'USA-PENDIENTE',
            'total_paquetes' => 2,
        ]);
        Package::create([
            'guia_principal' => 'USA-EN-MEXICO',
            'total_paquetes' => 3,
            'received_mex_at' => now(),
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

    public function test_only_administrators_can_modify_comparison_and_delete_guides(): void
    {
        $package = Package::create([
            'guia_principal' => 'USA-ADMINISTRABLE',
        ]);
        $supervisor = User::factory()->create(['role' => 'supervisor']);

        $documentador = User::factory()->create(['role' => 'documentador']);

        $this->actingAs($documentador)
            ->delete(route('reports.comparison.destroy', $package))
            ->assertForbidden();

        $this->actingAs($supervisor)
            ->delete(route('reports.comparison.destroy', $package))
            ->assertForbidden();

        $administrator = User::factory()->create(['role' => 'administrador']);

        $this->actingAs($administrator)
            ->delete(route('reports.comparison.destroy', $package))
            ->assertRedirect();

        $this->assertSoftDeleted('packages', ['id' => $package->id]);
    }
}
