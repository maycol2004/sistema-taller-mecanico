<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->string('placa', 10)->unique();
            $table->string('marca', 50);
            $table->string('modelo', 50);
            $table->smallInteger('anio');
            $table->string('color', 30)->nullable();
            $table->integer('kilometraje')->default(0);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE vehiculos ADD CONSTRAINT vehiculos_anio_check CHECK (anio BETWEEN 1900 AND 2100)');
        DB::statement('ALTER TABLE vehiculos ADD CONSTRAINT vehiculos_kilometraje_check CHECK (kilometraje >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
