<?php

namespace Tests\Feature;

use App\Models\Package;
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

    public function test_bodega_rejects_a_guide_that_does_not_exist(): void
    {
        $response = $this->post('/bodega', [
            'guia_principal' => 'NO-EXISTE-999',
        ]);

        $response->assertSessionHas('error', 'La guía no existe en el sistema. Debe registrarse primero en USA.');
    }
}
