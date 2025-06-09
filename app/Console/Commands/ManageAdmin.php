<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\users\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ManageAdmin extends Command
{

    protected $signature = 'app:manage-admin {email : The email of the user} {--revoke : Revoke the admin role from the user}';

    protected $description = 'Assigns or revokes the admin role and associated permissions to/from a user.';

    public function handle()
    {
        $email = $this->argument('email');
        $revoke = $this->option('revoke');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return Command::FAILURE;
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminPermissions = [
            'crear juegos',
            'editar juegos',
            'eliminar juegos',
        ];

        foreach ($adminPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        if ($revoke) {
            if ($user->hasRole('admin')) {
                $user->removeRole('admin');
                $user->revokePermissionTo($adminPermissions);
                $this->info("User {$user->email} no longer has the admin role and its associated permissions have been revoked.");
            } else {
                $this->info("User {$user->email} does not have the admin role to revoke.");
            }
        } else {
            if (!$user->hasRole('admin')) {
                $user->assignRole('admin');
                $adminRole->syncPermissions(Permission::whereIn('name', $adminPermissions)->get());
                $user->givePermissionTo($adminPermissions);

                $this->info("User {$user->email} now has the admin role and associated permissions.");
            } else {
                $this->info("User {$user->email} already has the admin role.");
            }
        }
        return Command::SUCCESS;
    }
}
