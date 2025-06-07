<?php

namespace App\Console\Commands;

use App\Models\users\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class ModerateUser extends Command
{
    protected $signature = 'app:moderate-user {email : El correo electrónico del usuario} {--revoke : Revoca el rol de moderador del usuario}';

    protected $description = 'Asigna o revoca el rol de moderador a un usuario.';


    public function handle()
    {
        $email = $this->argument('email');
        $revoke = $this->option('revoke');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Usuario con el correo {$email} no encontrado.");
            return Command::FAILURE;
        }

        $moderatorRole = Role::firstOrCreate(['name' => 'moderador']);

        if ($revoke) {
            if ($user->hasRole('moderador')) {
                $user->removeRole('moderador');
                $this->info("El usuario {$user->email} ya no tiene el rol de moderador.");
            } else {
                $this->info("El usuario {$user->email} no tiene el rol de moderador para revocar.");
            }
        } else {
            if (!$user->hasRole('moderador')) {
                $user->assignRole('moderador');
                $this->info("El usuario {$user->email} ahora tiene el rol de moderador.");
            } else {
                $this->info("El usuario {$user->email} ya tiene el rol de moderador.");
            }
        }

        return Command::SUCCESS;
    }
}
