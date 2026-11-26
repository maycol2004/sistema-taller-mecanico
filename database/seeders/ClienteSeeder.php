<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'documento' => '45678912',
                'nombres' => 'Juan Carlos',
                'apellidos' => 'Pérez Quispe',
                'telefono' => '987654321',
                'email' => 'juan.perez@gmail.com',
                'direccion' => 'Av. Los Próceres 245, San Juan de Lurigancho',
            ],
            [
                'documento' => '10456789123',
                'nombres' => 'María Elena',
                'apellidos' => 'Torres Ríos',
                'telefono' => '912345678',
                'email' => 'maria.torres@hotmail.com',
                'direccion' => 'Jr. Las Begonias 812, Miraflores',
            ],
            [
                'documento' => '72345678',
                'nombres' => 'Luis Alberto',
                'apellidos' => 'Ramírez Soto',
                'telefono' => '956789123',
                'email' => 'luis.ramirez@gmail.com',
                'direccion' => 'Av. Universitaria 1520, Comas',
            ],
            [
                'documento' => '20547896321',
                'nombres' => 'Transportes El Sol',
                'apellidos' => 'S.A.C.',
                'telefono' => '014567890',
                'email' => 'operaciones@transporteselsol.pe',
                'direccion' => 'Av. Argentina 4850, Callao',
            ],
            [
                'documento' => '41234567',
                'nombres' => 'Ana Lucía',
                'apellidos' => 'Mendoza Vega',
                'telefono' => '934567812',
                'email' => 'ana.mendoza@gmail.com',
                'direccion' => 'Calle Los Álamos 145, Santiago de Surco',
            ],
            [
                'documento' => '10987654321',
                'nombres' => 'Carlos Andrés',
                'apellidos' => 'Gutiérrez Paredes',
                'telefono' => '945678123',
                'email' => 'carlos.gutierrez@gmail.com',
                'direccion' => 'Av. Arequipa 3210, San Isidro',
            ],
            [
                'documento' => '87654321',
                'nombres' => 'Rosa Mercedes',
                'apellidos' => 'Flores Huamán',
                'telefono' => '967812345',
                'email' => 'rosa.flores@gmail.com',
                'direccion' => 'Jr. Cusco 456, Breña',
            ],
            [
                'documento' => '70123456',
                'nombres' => 'Jorge Luis',
                'apellidos' => 'Salazar Cárdenas',
                'telefono' => '923456789',
                'email' => 'jorge.salazar@gmail.com',
                'direccion' => 'Av. La Marina 1850, San Miguel',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::updateOrCreate(
                ['documento' => $cliente['documento']],
                $cliente
            );
        }
    }
}
