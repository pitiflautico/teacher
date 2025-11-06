<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@teacher.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        // Asignar rol de admin
        $admin->assignRole('admin');

        // Crear usuario estudiante de prueba
        $estudiante = User::create([
            'name' => 'Estudiante Demo',
            'email' => 'estudiante@teacher.com',
            'password' => Hash::make('estudiante123'),
            'email_verified_at' => now(),
        ]);

        // Asignar rol de estudiante
        $estudiante->assignRole('estudiante');
    }
}
