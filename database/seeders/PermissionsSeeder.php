<?php

namespace Database\Seeders;

use App\Models\users\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

    class PermissionsSeeder extends Seeder
    {
        public function run()
        {
            Permission::create(['name' => 'Crear Videojuegos']);
            Permission::create(['name' => 'Actualizar Videojuegos']);
            Permission::create(['name' => 'Eliminar Videojuegos']);

            $adminUser = User::query()->create([
               'name' => 'admin',
                'email' => 'admin@admin.com',
                'password' => '123456',
                'email_verified_at' => now(),
            ]);
            $roleAdmin = Role::create(['name' => 'admin']);
            $adminUser->assignRole($roleAdmin);
            $permissionsAdmin = Permission::query()->pluck('name');
            $roleAdmin->syncPermissions($permissionsAdmin);
        }
    }
