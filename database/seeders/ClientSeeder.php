<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['nombre' => 'Carlos Mendoza', 'alias' => 'Carlos M.', 'partner_id' => 1],
            ['nombre' => 'Diana Rodriguez', 'alias' => 'Diana R.', 'partner_id' => 1],
            ['nombre' => 'Empresa Importadora del Caribe', 'alias' => 'Importadora Caribe', 'partner_id' => 2],
            ['nombre' => 'Juan Perez', 'alias' => 'Juan P.', 'partner_id' => null],
            ['nombre' => 'Maria Gonzalez', 'alias' => 'Maria G.', 'partner_id' => 2],
            ['nombre' => 'Servicios Logisticos Nacionales', 'alias' => 'Servilog', 'partner_id' => null],
        ];

        foreach ($clients as $client) {
            Client::updateOrCreate(
                ['nombre' => $client['nombre']],
                [
                    'partner_id' => $client['partner_id'],
                    'nombre' => $client['nombre'],
                    'alias' => $client['alias'],
                ]
            );
        }
    }
}
