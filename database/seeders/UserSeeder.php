<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'name' => 'Administrador del Taller',
                'email' => 'admin@taller.com',
                'password' => 'admin123',
                'rol' => User::ROL_ADMIN,
            ],
            [
                'name' => 'Mecánico de Turno',
                'email' => 'mecanico@taller.com',
                'password' => 'mecanico123',
                'rol' => User::ROL_MECANICO,
            ],
        ];

        foreach ($usuarios as $usuario) {
            User::updateOrCreate(
                ['email' => $usuario['email']],
                [
                    'name' => $usuario['name'],
                    'password' => $usuario['password'],
                    'rol' => $usuario['rol'],
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
