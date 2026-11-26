<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('vehiculo_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->date('fecha_entrega_real')
                ->nullable()
                ->after('fecha_estimada_entrega');
        });

        DB::statement('ALTER TABLE ordenes ADD CONSTRAINT ordenes_fecha_entrega_real_check CHECK (fecha_entrega_real IS NULL OR fecha_entrega_real >= fecha_ingreso)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE ordenes DROP CONSTRAINT IF EXISTS ordenes_fecha_entrega_real_check');

        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('fecha_entrega_real');
        });
    }
};
