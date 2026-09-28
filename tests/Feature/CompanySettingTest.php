<?php

namespace Tests\Feature;

use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanySettingTest extends TestCase
{
    public function test_company_settings_are_only_available_to_administrators(): void
    {
        $operator = User::factory()->create(['role' => 'documentador']);

        $this->actingAs($operator)
            ->get(route('company-settings.edit'))
            ->assertForbidden();

        $administrator = User::factory()->create(['role' => 'administrador']);

        $this->actingAs($administrator)
            ->get(route('company-settings.edit'))
            ->assertOk();
    }

    public function test_administrator_can_save_company_details_and_png_brand_assets(): void
    {
        Storage::fake('public');
        $administrator = User::factory()->create(['role' => 'administrador']);
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC');

        $this->actingAs($administrator)
            ->put(route('company-settings.update'), [
                'name' => 'MC Importaciones',
                'legal_name' => 'MC Importaciones S.A.',
                'tax_id' => 'RFC123',
                'email' => 'contacto@example.com',
                'phone' => '5551234567',
                'website' => 'https://example.com',
                'address' => 'Ciudad de México',
                'logo_png' => UploadedFile::fake()->createWithContent('logo.png', $png),
                'favicon_png' => UploadedFile::fake()->createWithContent('favicon.png', $png),
            ])
            ->assertRedirect(route('company-settings.edit'));

        $this->assertDatabaseHas('company_settings', [
            'id' => 1,
            'name' => 'MC Importaciones',
            'tax_id' => 'RFC123',
        ]);

        $this->get(route('company-settings.logo'))->assertOk()->assertHeader('content-type', 'image/png');
        $this->get(route('company-settings.favicon'))->assertOk()->assertHeader('content-type', 'image/png');
        $this->assertCount(2, Storage::disk('public')->allFiles('company'));

        $settings = CompanySetting::query()->findOrFail(1);
        $previousPaths = [$settings->logo_path, $settings->favicon_path];

        $this->actingAs($administrator)
            ->put(route('company-settings.update'), [
                'name' => 'MC Importaciones Actualizada',
                'logo_png' => UploadedFile::fake()->createWithContent('logo-nuevo.png', $png),
                'favicon_png' => UploadedFile::fake()->createWithContent('favicon-nuevo.png', $png),
            ])
            ->assertRedirect(route('company-settings.edit'));

        foreach ($previousPaths as $path) {
            $this->assertFalse(Storage::disk('public')->exists($path));
        }
        $this->assertCount(2, Storage::disk('public')->allFiles('company'));
        $this->assertDatabaseHas('company_settings', ['name' => 'MC Importaciones Actualizada']);
    }
}