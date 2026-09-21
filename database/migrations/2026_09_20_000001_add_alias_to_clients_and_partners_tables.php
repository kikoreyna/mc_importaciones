<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('alias')->nullable()->after('nombre');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->string('alias')->nullable()->after('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('alias');
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('alias');
        });
    }
};
