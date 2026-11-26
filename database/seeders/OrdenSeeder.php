<?php

namespace Database\Seeders;

use App\Models\Orden;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Vehiculo;
use App\Services\InventarioService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdenSeeder extends Seeder
{
    public function run(): void
    {
        $ordenes = [
            [
                'placa' => 'ABC-123',
                'fecha_ingreso' => now()->subDays(10),
                'fecha_estimada_entrega' => now()->subDays(8),
                'diagnostico' => 'Mantenimiento preventivo de 80,000 km. Se recomienda reemplazar el filtro de aire.',
                'observaciones' => 'Cliente solicita revisión adicional de niveles antes de entregar.',
                'estado' => 'Entregado',
                'servicios' => [
                    ['nombre' => 'Cambio de aceite y filtro', 'cantidad' => 1],
                    ['nombre' => 'Diagnóstico por escáner', 'cantidad' => 1],
                ],
                'repuestos' => [
                    ['codigo' => 'REP-001', 'cantidad' => 1],
                    ['codigo' => 'REP-002', 'cantidad' => 4],
                    ['codigo' => 'REP-007', 'cantidad' => 1],
                ],
            ],
            [
                'placa' => 'CDF-345',
                'fecha_ingreso' => now()->subDays(9),
                'fecha_estimada_entrega' => now()->subDays(7),
                'diagnostico' => 'Ruido al frenar y pedal con recorrido esponjoso.',
                'observaciones' => 'Se recomienda revisar los discos en el próximo servicio.',
                'estado' => 'Entregado',
                'servicios' => [
                    ['nombre' => 'Cambio de pastillas de freno', 'cantidad' => 1],
                ],
                'repuestos' => [
                    ['codigo' => 'REP-003', 'cantidad' => 1],
                ],
            ],
            [
                'placa' => 'BCD-234',
                'fecha_ingreso' => now()->subDays(7),
                'fecha_estimada_entrega' => now()->subDays(5),
                'diagnostico' => 'Afinamiento mayor por pérdida de potencia y consumo elevado.',
                'observaciones' => 'Bujías y filtro de aire reemplazados.',
                'estado' => 'Finalizado',
                'servicios' => [
                    ['nombre' => 'Afinamiento mayor', 'cantidad' => 1],
                ],
                'repuestos' => [
                    ['codigo' => 'REP-004', 'cantidad' => 4],
                    ['codigo' => 'REP-007', 'cantidad' => 1],
                ],
            ],
            [
                'placa' => 'DGH-456',
                'fecha_ingreso' => now()->subDays(5),
                'fecha_estimada_entrega' => now()->subDay(),
                'diagnostico' => 'Golpeteo en la suspensión delantera al pasar rompemuelles.',
                'observaciones' => 'Pendiente prueba de ruta con el cliente.',
                'estado' => 'En Proceso',
                'servicios' => [
                    ['nombre' => 'Reparación de suspensión', 'cantidad' => 1],
                ],
                'repuestos' => [
                    ['codigo' => 'REP-006', 'cantidad' => 2],
                ],
            ],
            [
                'placa' => 'FJK-567',
                'fecha_ingreso' => now()->subDays(4),
                'fecha_estimada_entrega' => now()->addDay(),
                'diagnostico' => 'Mantenimiento de flota: cambio de aceite, filtros y frenos delanteros.',
                'observaciones' => 'Vehículo de empresa; facturar a nombre de Transportes El Sol S.A.C.',
                'estado' => 'En Proceso',
                'servicios' => [
                    ['nombre' => 'Cambio de aceite y filtro', 'cantidad' => 1],
                    ['nombre' => 'Cambio de pastillas de freno', 'cantidad' => 1],
                ],
                'repuestos' => [
                    ['codigo' => 'REP-001', 'cantidad' => 1],
                    ['codigo' => 'REP-002', 'cantidad' => 5],
                    ['codigo' => 'REP-003', 'cantidad' => 1],
                ],
            ],
            [
                'placa' => 'GHJ-678',
                'fecha_ingreso' => now()->subDays(3),
                'fecha_estimada_entrega' => now()->addDay(),
                'diagnostico' => 'Testigo de motor encendido; revisar sensores y sistema de encendido.',
                'observaciones' => 'Cliente autorizó diagnóstico inicial únicamente.',
                'estado' => 'Pendiente',
                'servicios' => [
                    ['nombre' => 'Diagnóstico por escáner', 'cantidad' => 1],
                ],
                'repuestos' => [],
            ],
            [
                'placa' => 'HJK-789',
                'fecha_ingreso' => now()->subDays(2),
                'fecha_estimada_entrega' => now()->addDays(2),
                'diagnostico' => 'Alineación y balanceo solicitados; batería con arranque lento.',
                'observaciones' => 'Confirmar disponibilidad de batería antes del cambio.',
                'estado' => 'Pendiente',
                'servicios' => [
                    ['nombre' => 'Alineación y balanceo', 'cantidad' => 1],
                    ['nombre' => 'Cambio de batería', 'cantidad' => 1],
                ],
                'repuestos' => [
                    ['codigo' => 'REP-005', 'cantidad' => 1],
                ],
            ],
            [
                'placa' => 'KLM-901',
                'fecha_ingreso' => now()->subDays(12),
                'fecha_estimada_entrega' => now()->subDays(9),
                'diagnostico' => 'Cambio de correa de distribución por kilometraje.',
                'observaciones' => 'Se reemplazó también el refrigerante.',
                'estado' => 'Entregado',
                'servicios' => [
                    ['nombre' => 'Cambio de correa de distribución', 'cantidad' => 1],
                ],
                'repuestos' => [
                    ['codigo' => 'REP-008', 'cantidad' => 1],
                    ['codigo' => 'REP-010', 'cantidad' => 2],
                ],
            ],
        ];

        $usuarios = User::query()->orderBy('id')->pluck('id')->all();
        $inventario = app(InventarioService::class);

        DB::transaction(function () use ($ordenes, $usuarios, $inventario): void {
            $secuencias = [];

            foreach ($ordenes as $indice => $datos) {
                $vehiculo = Vehiculo::where('placa', $datos['placa'])->firstOrFail();

                $anio = $datos['fecha_ingreso']->format('Y');
                $secuencias[$anio] = ($secuencias[$anio] ?? 0) + 1;
                $codigo = sprintf('OT-%s-%04d', $anio, $secuencias[$anio]);

                $orden = Orden::updateOrCreate(
                    ['codigo' => $codigo],
                    [
                        'vehiculo_id' => $vehiculo->id,
                        'user_id' => $usuarios === [] ? null : $usuarios[$indice % count($usuarios)],
                        'fecha_ingreso' => $datos['fecha_ingreso']->toDateString(),
                        'fecha_estimada_entrega' => $datos['fecha_estimada_entrega']->toDateString(),
                        'fecha_entrega_real' => $datos['estado'] === 'Entregado'
                            ? $datos['fecha_estimada_entrega']->toDateString()
                            : null,
                        'diagnostico' => $datos['diagnostico'],
                        'observaciones' => $datos['observaciones'],
                        'estado' => $datos['estado'],
                    ]
                );

                $servicios = [];

                foreach ($datos['servicios'] as $detalle) {
                    $servicio = Servicio::where('nombre', $detalle['nombre'])->firstOrFail();

                    $servicios[] = [
                        'servicio_id' => $servicio->id,
                        'cantidad' => (int) $detalle['cantidad'],
                    ];
                }

                $repuestos = [];

                foreach ($datos['repuestos'] as $detalle) {
                    $repuesto = Repuesto::where('codigo', $detalle['codigo'])->firstOrFail();

                    $repuestos[] = [
                        'repuesto_id' => $repuesto->id,
                        'cantidad' => (int) $detalle['cantidad'],
                    ];
                }

                $inventario->sincronizarDetalle($orden, $servicios, $repuestos);
                $orden->recalcularTotal();
            }
        });
    }
}
