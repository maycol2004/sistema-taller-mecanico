<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repuestos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 120);
            $table->string('marca', 60)->nullable();
            $table->decimal('precio_unitario', 10, 2);
            $table->integer('stock')->default(0);
            $table->timestamps();

            $table->index('nombre');
        });

        DB::statement('ALTER TABLE repuestos ADD CONSTRAINT repuestos_precio_unitario_check CHECK (precio_unitario >= 0)');
        DB::statement('ALTER TABLE repuestos ADD CONSTRAINT repuestos_stock_check CHECK (stock >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('repuestos');
    }
};
