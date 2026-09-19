<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete()->after('guia_secundaria');
            $table->foreignId('partner_id')->nullable()->constrained()->nullOnDelete()->after('client_id');
            $table->string('transportadora')->nullable()->after('partner_id');
            $table->unsignedInteger('caja_numero')->nullable()->after('transportadora');
            $table->unsignedInteger('total_cajas')->nullable()->after('caja_numero');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
            $table->dropConstrainedForeignId('partner_id');
            $table->dropColumn(['transportadora', 'caja_numero', 'total_cajas']);
        });
    }
};
