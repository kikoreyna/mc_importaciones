<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique('clients_codigo_unique');
            $table->dropColumn('codigo');
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->dropUnique('partners_codigo_unique');
            $table->dropColumn('codigo');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('codigo')->nullable()->unique()->after('alias');
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->string('codigo')->nullable()->unique()->after('alias');
        });
    }
};
