<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'operador')->update(['role' => 'documentador']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('documentador')->change();
        });
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'documentador')->update(['role' => 'operador']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('operador')->change();
        });
    }
};