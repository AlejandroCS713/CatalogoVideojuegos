<?php
namespace Database\Seeders;

use App\Models\users\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos
        $permissions = [
            'crear juegos',
            'editar juegos',
            'eliminar juegos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear rol de admin y asignar permisos
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        // Asignar rol admin al usuario con ID 1 (cambiar si es necesario)
        $user = User::find(1);
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
