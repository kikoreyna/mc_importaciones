<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['nombre' => 'Carlos Mendoza', 'codigo' => 'CLI-001'],
            ['nombre' => 'Diana Rodriguez', 'codigo' => 'CLI-002'],
            ['nombre' => 'Empresa Importadora del Caribe', 'codigo' => 'CLI-003'],
            ['nombre' => 'Juan Perez', 'codigo' => 'CLI-004'],
            ['nombre' => 'Maria Gonzalez', 'codigo' => 'CLI-005'],
            ['nombre' => 'Servicios Logisticos Nacionales', 'codigo' => 'CLI-006'],
        ];

        foreach ($clients as $client) {
            Client::updateOrCreate(
                ['codigo' => $client['codigo']],
                ['nombre' => $client['nombre']]
            );
        }
    }
}
