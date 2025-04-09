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

            $roleAdmin = Role::create(['name' => 'admin']);
            $roleUser = Role::firstOrCreate(['name' => 'user']);
            $rolemoderador = Role::firstOrCreate(['name' => 'moderador']);

            $adminUser = User::query()->create([
               'name' => 'admin',
                'email' => 'admin@admin.com',
                'password' => '123456',
                'email_verified_at' => now(),
                'avatar' => 'avatarAngel.png',
            ]);

            $adminUser->assignRole(['admin', 'user']);

            $permissionsAdmin = Permission::query()->pluck('name');
            $roleAdmin->syncPermissions($permissionsAdmin);
        }
    }
