<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        $servicios = [
            [
                'nombre' => 'Cambio de aceite y filtro',
                'descripcion' => 'Cambio de aceite de motor, filtro de aceite y revisión de niveles.',
                'precio_base' => 120.00,
                'duracion_estimada' => '45 minutos',
            ],
            [
                'nombre' => 'Afinamiento mayor',
                'descripcion' => 'Limpieza de inyectores, cambio de bujías, filtros y revisión general.',
                'precio_base' => 380.00,
                'duracion_estimada' => '3 horas',
            ],
            [
                'nombre' => 'Cambio de pastillas de freno',
                'descripcion' => 'Sustitución de pastillas de freno delanteras y revisión de discos.',
                'precio_base' => 180.00,
                'duracion_estimada' => '1 hora 30 minutos',
            ],
            [
                'nombre' => 'Alineación y balanceo',
                'descripcion' => 'Alineación de dirección y balanceo de las cuatro ruedas.',
                'precio_base' => 90.00,
                'duracion_estimada' => '1 hora',
            ],
            [
                'nombre' => 'Cambio de batería',
                'descripcion' => 'Reemplazo de batería y prueba del sistema de carga.',
                'precio_base' => 60.00,
                'duracion_estimada' => '30 minutos',
            ],
            [
                'nombre' => 'Reparación de suspensión',
                'descripcion' => 'Diagnóstico y reparación de amortiguadores, bujes y terminales.',
                'precio_base' => 450.00,
                'duracion_estimada' => '4 horas',
            ],
            [
                'nombre' => 'Diagnóstico por escáner',
                'descripcion' => 'Lectura de códigos de falla y diagnóstico electrónico del vehículo.',
                'precio_base' => 80.00,
                'duracion_estimada' => '40 minutos',
            ],
            [
                'nombre' => 'Cambio de correa de distribución',
                'descripcion' => 'Sustitución de correa, tensor y revisión de la bomba de agua.',
                'precio_base' => 620.00,
                'duracion_estimada' => '5 horas',
            ],
        ];

        foreach ($servicios as $servicio) {
            Servicio::updateOrCreate(
                ['nombre' => $servicio['nombre']],
                $servicio
            );
        }
    }
}
