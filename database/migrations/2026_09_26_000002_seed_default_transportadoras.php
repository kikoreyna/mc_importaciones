<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $transportadoras = [
            ['nombre' => 'DHL', 'web' => 'https://www.dhl.com'],
            ['nombre' => 'FedEx', 'web' => 'https://www.fedex.com'],
            ['nombre' => 'UPS', 'web' => 'https://www.ups.com'],
            ['nombre' => 'USPS', 'web' => 'https://www.usps.com'],
            ['nombre' => 'Amazon Logistics', 'web' => 'https://logistics.amazon.com'],
            ['nombre' => 'Otra', 'web' => 'https://example.com'],
        ];

        foreach ($transportadoras as $transportadora) {
            DB::table('transportadoras')->updateOrInsert(
                ['nombre' => $transportadora['nombre']],
                [
                    'web' => $transportadora['web'],
                    'telefono' => null,
                    'deleted_at' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        DB::table('transportadoras')
            ->whereIn('nombre', ['DHL', 'FedEx', 'UPS', 'USPS', 'Amazon Logistics', 'Otra'])
            ->delete();
    }
};
