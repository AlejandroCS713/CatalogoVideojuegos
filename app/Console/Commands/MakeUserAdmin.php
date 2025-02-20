<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\users\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MakeUserAdmin extends Command
{protected $signature = 'make:admin {email}';
    protected $description = 'Asignar el rol de admin a un usuario y dar permisos';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Usuario con el correo {$email} no encontrado.");
            return;
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $permissions = [
            'crear juegos',
            'editar juegos',
            'eliminar juegos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole->givePermissionTo($permissions);

        $user->assignRole('admin');

        $this->info("El usuario {$user->email} ahora tiene el rol de admin y los permisos necesarios.");
    }
}
