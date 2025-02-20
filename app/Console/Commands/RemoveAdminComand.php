<?php

namespace App\Console\Commands;

use App\Models\users\User;
use Illuminate\Console\Command;

class RemoveAdminComand extends Command
{
    protected $signature = 'remove:admin {email}';
    protected $description = 'Eliminar el rol de admin de un usuario y revocar sus permisos';

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

        if ($user->hasRole('admin')) {

            $user->removeRole('admin');

            $user->revokePermissionTo('crear juegos');
            $user->revokePermissionTo('editar juegos');
            $user->revokePermissionTo('eliminar juegos');

            $this->info("El usuario {$user->email} ya no es un admin.");
        } else {
            $this->info("El usuario {$user->email} no tiene rol de admin.");
        }
    }
}
