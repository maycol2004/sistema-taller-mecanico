<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->restrictOnDelete();
            $table->date('fecha_ingreso');
            $table->date('fecha_estimada_entrega')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('estado', 20)->default('Pendiente');
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();

            $table->index('estado');
            $table->index('fecha_ingreso');
        });

        DB::statement("ALTER TABLE ordenes ADD CONSTRAINT ordenes_estado_check CHECK (estado IN ('Pendiente', 'En Proceso', 'Finalizado', 'Entregado'))");
        DB::statement('ALTER TABLE ordenes ADD CONSTRAINT ordenes_fechas_check CHECK (fecha_estimada_entrega IS NULL OR fecha_estimada_entrega >= fecha_ingreso)');
        DB::statement('ALTER TABLE ordenes ADD CONSTRAINT ordenes_total_check CHECK (total >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
