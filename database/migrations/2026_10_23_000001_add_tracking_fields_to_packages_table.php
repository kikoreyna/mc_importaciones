<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->foreignId('transportadora_id')->nullable()->constrained('transportadoras')->nullOnDelete();
            $table->foreignId('bodega_usa_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('bodega_mex_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('documentador_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('documented_at')->nullable();
        });

        DB::table('packages')->update(['registered_at' => DB::raw('created_at')]);
        DB::table('packages')->whereIn('estado', ['recibido', 'ingreso_bodega', 'documentado'])->update(['received_at' => DB::raw('created_at')]);
        DB::table('packages')->where('estado', 'documentado')->update(['documented_at' => DB::raw('updated_at')]);
        DB::statement('UPDATE packages SET transportadora_id = (SELECT t.id FROM transportadoras t WHERE t.nombre = packages.transportadora LIMIT 1)');
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('transportadora_id');
            $table->dropConstrainedForeignId('bodega_usa_user_id');
            $table->dropConstrainedForeignId('bodega_mex_user_id');
            $table->dropConstrainedForeignId('documentador_user_id');
            $table->dropColumn(['registered_at', 'received_at', 'documented_at']);
        });
    }
};
