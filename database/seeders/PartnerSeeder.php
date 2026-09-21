<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        $partners = [
            ['nombre' => 'Importaciones del Norte', 'alias' => 'Impornorte'],
            ['nombre' => 'Logistica Integral USA', 'alias' => 'LIUSA'],
        ];

        foreach ($partners as $partner) {
            Partner::updateOrCreate(
                ['nombre' => $partner['nombre']],
                [
                    'nombre' => $partner['nombre'],
                    'alias' => $partner['alias'],
                ]
            );
        }
    }
}
