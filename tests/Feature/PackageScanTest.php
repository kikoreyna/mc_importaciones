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

    public function test_package_lookup_matches_primary_secondary_and_master_guides(): void
    {
        $package = Package::create([
            'guia_principal' => 'USA-LOOKUP-1',
            'guia_secundaria' => 'SEC-LOOKUP-1',
            'guia_master' => 'MASTER-LOOKUP-1',
        ]);

        foreach (['USA-LOOKUP-1', 'SEC-LOOKUP-1', 'MASTER-LOOKUP-1'] as $guide) {
            $this->getJson(route('packages.lookup', ['q' => $guide]))
                ->assertOk()
                ->assertExactJson([
                    'found' => true,
                    'package' => [
                        'id' => $package->id,
                        'guia_principal' => 'USA-LOOKUP-1',
                        'guia_secundaria' => 'SEC-LOOKUP-1',
                        'guia_master' => 'MASTER-LOOKUP-1',
                    ],
                ]);
        }

        $this->getJson(route('packages.lookup', ['q' => 'NO-EXISTE']))
            ->assertOk()
            ->assertExactJson(['found' => false]);
    }

    public function test_can_add_secondary_and_master_guides_to_an_existing_package_from_scanner(): void
    {
        $package = Package::create(['guia_principal' => 'USA-GUIDES-RETURN']);

        $this->post(route('packages.guides.scan.store', $package), [
            'guide_type' => 'secondary',
            'guide_action' => 'add',
            'guia_secundaria' => 'SEC-GUIDES-RETURN',
        ])->assertRedirect(route('packages.index'));

        $this->post(route('packages.guides.scan.store', $package), [
            'guide_type' => 'master',
            'guide_action' => 'add',
            'guia_master' => 'MASTER-GUIDES-RETURN',
        ])->assertRedirect(route('packages.index'));

        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'guia_secundaria' => 'SEC-GUIDES-RETURN',
            'guia_master' => 'MASTER-GUIDES-RETURN',
        ]);
    }

    public function test_scanner_cannot_replace_an_existing_or_reuse_another_packages_guide(): void
    {
        $existing = Package::create([
            'guia_principal' => 'USA-GUIDE-EXISTING',
            'guia_secundaria' => 'SEC-GUIDE-EXISTING',
        ]);
        $target = Package::create(['guia_principal' => 'USA-GUIDE-TARGET']);

        $this->post(route('packages.guides.scan.store', $existing), [
            'guide_type' => 'secondary',
            'guide_action' => 'add',
            'guia_secundaria' => 'SEC-REPLACEMENT',
        ])->assertSessionHasErrors('guia_secundaria');

        $this->post(route('packages.guides.scan.store', $target), [
            'guide_type' => 'master',
            'guide_action' => 'add',
            'guia_master' => 'USA-GUIDE-EXISTING',
        ])->assertSessionHasErrors('guia_master');

        $this->assertDatabaseHas('packages', [
            'id' => $existing->id,
            'guia_secundaria' => 'SEC-GUIDE-EXISTING',
        ]);
        $this->assertDatabaseHas('packages', [
            'id' => $target->id,
            'guia_master' => null,
        ]);
    }

    public function test_can_correct_existing_guides_and_record_the_changes(): void
    {
        $package = Package::create([
            'guia_principal' => 'USA-CORRECT-GUIDES',
            'guia_secundaria' => 'SEC-INCORRECTA',
            'guia_master' => 'MASTER-INCORRECTA',
        ]);

        $this->post(route('packages.guides.scan.store', $package), [
            'guide_type' => 'secondary',
            'guide_action' => 'correct',
            'guia_secundaria' => 'SEC-CORRECTA',
        ])->assertRedirect(route('packages.index'));

        $this->post(route('packages.guides.scan.store', $package), [
            'guide_type' => 'master',
            'guide_action' => 'correct',
            'guia_master' => 'MASTER-CORRECTA',
        ])->assertRedirect(route('packages.index'));

        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'guia_secundaria' => 'SEC-CORRECTA',
            'guia_master' => 'MASTER-CORRECTA',
        ]);

        $changes = $package->logs()->where('action', 'actualizada')->orderBy('id')->get()->pluck('changes')->all();
        $this->assertSame('SEC-INCORRECTA', $changes[0]['Guía secundaria'][0]);
        $this->assertSame('SEC-CORRECTA', $changes[0]['Guía secundaria'][1]);
        $this->assertSame('MASTER-INCORRECTA', $changes[1]['Guía máster'][0]);
        $this->assertSame('MASTER-CORRECTA', $changes[1]['Guía máster'][1]);
    }

    public function test_adding_photos_from_the_scanner_returns_to_usa_reception(): void
    {
        Storage::fake('local');
        $package = Package::create(['guia_principal' => 'USA-PHOTOS-RETURN']);

        $this->post(route('packages.photos.scan.store', $package), [
            'photos' => [UploadedFile::fake()->create('photo.jpg', 10, 'image/jpeg')],
        ])->assertRedirect(route('packages.index'));

        $this->assertCount(1, $package->fresh()->photos);

        $this->post(route('packages.photos.store', $package), [
            'photos' => [UploadedFile::fake()->create('photo-registered.jpg', 10, 'image/jpeg')],
        ])->assertRedirect(route('packages.registered'));

        $this->assertCount(2, $package->fresh()->photos);
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
