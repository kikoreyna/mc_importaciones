<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Package;
use App\Models\PackagePhoto;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    private const TOTAL = 100;

    public function run(): void
    {
        $clients = Client::all();
        $transportadoras = ['DHL', 'FedEx', 'UPS', 'Estafeta', 'Paquetexpress'];
        $colores = ['azul', 'azul', 'amarillo', 'amarillo', 'verde'];

        // Reemplaza solo los paquetes de siembra anteriores; usuarios y clientes no se tocan.
        Package::withTrashed()->where('guia_principal', 'like', 'SEED-%')->get()->each(function (Package $package) {
            $package->photos()->delete();
            $package->forceDelete();
        });

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $withInfo = $i % 3 !== 0;
            $client = $withInfo && $clients->isNotEmpty() && $i % 5 !== 0 ? $clients->random() : null;
            $totalCajas = $withInfo && $i % 4 === 0 ? random_int(2, 8) : null;
            $createdAt = now()->subHours(random_int(1, 24 * 20));

            $color = $colores[array_rand($colores)];

            $package = Package::create([
                'guia_principal' => sprintf('SEED-%04d', $i),
                'guia_secundaria' => $i % 2 === 0 ? sprintf('SEC-%06d', random_int(100000, 999999)) : null,
                'guia_master' => $withInfo && $i % 6 === 0 ? sprintf('MST-%05d', random_int(10000, 99999)) : null,
                'received_mex_at' => $color !== 'azul' ? $createdAt : null,
                'documented_at' => $color === 'verde' ? $createdAt : null,
                'client_id' => $client?->id,
                'partner_id' => $client?->partner_id,
                'transportadora' => $withInfo ? $transportadoras[array_rand($transportadoras)] : null,
                'total_cajas' => $totalCajas,
                'caja_numero' => $totalCajas ? random_int(1, $totalCajas) : null,
            ]);

            $package->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();

            // Sin fotos, 1 foto o varias, de forma variada.
            $photoCount = match ($i % 4) {
                0 => 0,
                1 => 1,
                2 => random_int(2, 3),
                default => random_int(3, 6),
            };

            for ($p = 1; $p <= $photoCount; $p++) {
                $url = sprintf('https://picsum.photos/seed/seed-%d-%d/800/600', $i, $p);
                $photo = PackagePhoto::create([
                    'package_id' => $package->id,
                    'file_name' => sprintf('seed-%d-%d.jpg', $i, $p),
                    'path' => sprintf('seed/seed-%d-%d.jpg', $i, $p),
                    'url' => $url,
                    'mime_type' => 'image/jpeg',
                    'size' => random_int(80_000, 900_000),
                ]);

                $photoAt = (clone $createdAt)->addMinutes($p * 5);
                $photo->forceFill(['created_at' => $photoAt, 'updated_at' => $photoAt])->saveQuietly();
            }
        }
    }
}
