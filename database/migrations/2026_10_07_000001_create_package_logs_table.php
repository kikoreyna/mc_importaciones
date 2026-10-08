<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // Copia del nombre y rol para conservar el histórico aunque el usuario cambie o se elimine.
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->string('action', 50);
            $table->string('description');
            $table->json('changes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['package_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_logs');
    }
};
