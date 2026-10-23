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
            $table->timestamp('received_mex_at')->nullable()->after('received_at');
        });

        DB::table('packages')->where('estado', 'ingreso_bodega')->update(['received_mex_at' => DB::raw('updated_at')]);
        DB::table('packages')->where('estado', 'documentado')->whereNull('documented_at')->update(['documented_at' => DB::raw('updated_at')]);
        DB::table('packages')->whereNull('received_at')->update(['received_at' => DB::raw('created_at')]);

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('estado')->default('recibido');
        });

        DB::table('packages')->whereNotNull('received_mex_at')->update(['estado' => 'ingreso_bodega']);
        DB::table('packages')->whereNotNull('documented_at')->update(['estado' => 'documentado']);

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('received_mex_at');
        });
    }
};
