<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Descuenta una sola vez el stock ya consumido por las órdenes existentes.
     * En una instalación nueva no hay filas y la migración no tiene efecto.
     */
    public function up(): void
    {
        DB::transaction(function (): void {
            $usos = DB::table('orden_repuesto')
                ->select('repuesto_id', DB::raw('SUM(cantidad) as total'))
                ->groupBy('repuesto_id')
                ->get();

            foreach ($usos as $uso) {
                DB::update(
                    'UPDATE repuestos SET stock = GREATEST(stock - ?, 0) WHERE id = ?',
                    [(int) $uso->total, (int) $uso->repuesto_id]
                );
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            $usos = DB::table('orden_repuesto')
                ->select('repuesto_id', DB::raw('SUM(cantidad) as total'))
                ->groupBy('repuesto_id')
                ->get();

            foreach ($usos as $uso) {
                DB::update(
                    'UPDATE repuestos SET stock = stock + ? WHERE id = ?',
                    [(int) $uso->total, (int) $uso->repuesto_id]
                );
            }
        });
    }
};
