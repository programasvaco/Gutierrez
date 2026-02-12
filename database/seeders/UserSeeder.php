<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador por defecto
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@sistema.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'activo',
            'telefono' => '1234567890',
        ]);

        // Crear usuario normal de ejemplo
        User::create([
            'name' => 'Usuario Demo',
            'email' => 'usuario@sistema.com',
            'password' => Hash::make('usuario123'),
            'role' => 'usuario',
            'status' => 'activo',
            'telefono' => '0987654321',
        ]);
    }
}
