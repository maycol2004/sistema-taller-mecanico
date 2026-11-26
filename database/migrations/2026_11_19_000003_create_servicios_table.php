<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120)->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('precio_base', 10, 2);
            $table->string('duracion_estimada', 50)->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE servicios ADD CONSTRAINT servicios_precio_base_check CHECK (precio_base >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
