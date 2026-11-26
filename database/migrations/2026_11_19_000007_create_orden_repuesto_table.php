<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_repuesto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes')->cascadeOnDelete();
            $table->foreignId('repuesto_id')->constrained('repuestos')->restrictOnDelete();
            $table->unsignedInteger('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            $table->unique(['orden_id', 'repuesto_id']);
        });

        DB::statement('ALTER TABLE orden_repuesto ADD CONSTRAINT orden_repuesto_cantidad_check CHECK (cantidad > 0)');
        DB::statement('ALTER TABLE orden_repuesto ADD CONSTRAINT orden_repuesto_precio_check CHECK (precio_unitario >= 0)');
        DB::statement('ALTER TABLE orden_repuesto ADD CONSTRAINT orden_repuesto_subtotal_check CHECK (subtotal >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_repuesto');
    }
};
