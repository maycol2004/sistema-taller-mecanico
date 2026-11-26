<?php

namespace Database\Seeders;

use App\Models\Repuesto;
use Illuminate\Database\Seeder;

class RepuestoSeeder extends Seeder
{
    public function run(): void
    {
        $repuestos = [
            ['codigo' => 'REP-001', 'nombre' => 'Filtro de aceite', 'marca' => 'Toyota', 'precio_unitario' => 35.00, 'stock' => 24],
            ['codigo' => 'REP-002', 'nombre' => 'Aceite motor 5W-30 (1L)', 'marca' => 'Mobil', 'precio_unitario' => 48.00, 'stock' => 40],
            ['codigo' => 'REP-003', 'nombre' => 'Pastillas de freno delanteras', 'marca' => 'Bosch', 'precio_unitario' => 160.00, 'stock' => 12],
            ['codigo' => 'REP-004', 'nombre' => 'Bujía de iridio', 'marca' => 'NGK', 'precio_unitario' => 42.00, 'stock' => 30],
            ['codigo' => 'REP-005', 'nombre' => 'Batería 13 placas', 'marca' => 'Bosch', 'precio_unitario' => 480.00, 'stock' => 8],
            ['codigo' => 'REP-006', 'nombre' => 'Amortiguador delantero', 'marca' => 'KYB', 'precio_unitario' => 320.00, 'stock' => 6],
            ['codigo' => 'REP-007', 'nombre' => 'Filtro de aire', 'marca' => 'Mann-Filter', 'precio_unitario' => 55.00, 'stock' => 18],
            ['codigo' => 'REP-008', 'nombre' => 'Correa de distribución', 'marca' => 'Gates', 'precio_unitario' => 210.00, 'stock' => 5],
            ['codigo' => 'REP-009', 'nombre' => 'Disco de freno', 'marca' => 'Brembo', 'precio_unitario' => 290.00, 'stock' => 4],
            ['codigo' => 'REP-010', 'nombre' => 'Refrigerante 50/50 (1L)', 'marca' => 'Prestone', 'precio_unitario' => 25.00, 'stock' => 3],
        ];

        foreach ($repuestos as $repuesto) {
            $registrado = Repuesto::firstOrCreate(
                ['codigo' => $repuesto['codigo']],
                $repuesto
            );

            if (! $registrado->wasRecentlyCreated) {
                $registrado->update([
                    'nombre' => $repuesto['nombre'],
                    'marca' => $repuesto['marca'],
                    'precio_unitario' => $repuesto['precio_unitario'],
                ]);
            }
        }
    }
}
