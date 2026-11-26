<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;

class VehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $vehiculos = [
            ['documento' => '45678912', 'placa' => 'ABC-123', 'marca' => 'Toyota', 'modelo' => 'Corolla', 'anio' => 2019, 'color' => 'Blanco', 'kilometraje' => 78500],
            ['documento' => '45678912', 'placa' => 'BCD-234', 'marca' => 'Hyundai', 'modelo' => 'Accent', 'anio' => 2021, 'color' => 'Gris', 'kilometraje' => 42300],
            ['documento' => '10456789123', 'placa' => 'CDF-345', 'marca' => 'Nissan', 'modelo' => 'Sentra', 'anio' => 2018, 'color' => 'Negro', 'kilometraje' => 96500],
            ['documento' => '72345678', 'placa' => 'DGH-456', 'marca' => 'Kia', 'modelo' => 'Sportage', 'anio' => 2022, 'color' => 'Azul', 'kilometraje' => 31800],
            ['documento' => '20547896321', 'placa' => 'FJK-567', 'marca' => 'Toyota', 'modelo' => 'Hilux', 'anio' => 2020, 'color' => 'Plata', 'kilometraje' => 112000],
            ['documento' => '41234567', 'placa' => 'GHJ-678', 'marca' => 'Chevrolet', 'modelo' => 'Spark GT', 'anio' => 2017, 'color' => 'Rojo', 'kilometraje' => 128500],
            ['documento' => '10987654321', 'placa' => 'HJK-789', 'marca' => 'Mazda', 'modelo' => 'CX-5', 'anio' => 2023, 'color' => 'Rojo', 'kilometraje' => 18500],
            ['documento' => '87654321', 'placa' => 'JKL-890', 'marca' => 'Suzuki', 'modelo' => 'Swift', 'anio' => 2019, 'color' => 'Blanco', 'kilometraje' => 64200],
            ['documento' => '20547896321', 'placa' => 'KLM-901', 'marca' => 'Mitsubishi', 'modelo' => 'L200', 'anio' => 2021, 'color' => 'Gris', 'kilometraje' => 87400],
            ['documento' => '70123456', 'placa' => 'LMN-012', 'marca' => 'Volkswagen', 'modelo' => 'Gol', 'anio' => 2016, 'color' => 'Azul', 'kilometraje' => 143000],
        ];

        foreach ($vehiculos as $vehiculo) {
            $cliente = Cliente::where('documento', $vehiculo['documento'])->firstOrFail();

            Vehiculo::updateOrCreate(
                ['placa' => $vehiculo['placa']],
                [
                    'cliente_id' => $cliente->id,
                    'marca' => $vehiculo['marca'],
                    'modelo' => $vehiculo['modelo'],
                    'anio' => $vehiculo['anio'],
                    'color' => $vehiculo['color'],
                    'kilometraje' => $vehiculo['kilometraje'],
                ]
            );
        }
    }
}
