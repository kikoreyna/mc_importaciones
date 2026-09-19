<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('guia_master')->nullable()->after('guia_secundaria');
            $table->unsignedInteger('total_paquetes')->default(1)->after('guia_master');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['guia_master', 'total_paquetes']);
        });
    }
};
