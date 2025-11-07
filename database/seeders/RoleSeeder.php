<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        $admin = Role::create(['name' => 'admin']);
        $estudiante = Role::create(['name' => 'estudiante']);

        // Crear permisos básicos
        $permissions = [
            // Gestión de usuarios
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Gestión de asignaturas
            'view_subjects',
            'create_subjects',
            'edit_subjects',
            'delete_subjects',

            // Gestión de material
            'view_materials',
            'create_materials',
            'edit_materials',
            'delete_materials',

            // Gestión de ejercicios
            'view_exercises',
            'create_exercises',
            'edit_exercises',
            'delete_exercises',
            'generate_exercises',

            // Acceso al panel de administración
            'access_admin_panel',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Asignar todos los permisos al admin
        $admin->givePermissionTo(Permission::all());

        // Asignar permisos limitados al estudiante
        $estudiante->givePermissionTo([
            'view_subjects',
            'view_materials',
            'create_materials',
            'edit_materials',
            'delete_materials',
            'view_exercises',
            'generate_exercises',
        ]);
    }
}
