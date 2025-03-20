<?php

namespace Database\Seeders;

use App\Models\users\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => 40,
                'name' => 'User 40',
                'email' => 'user40@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => Carbon::now(),
                'avatar' => 'avatarAngel.png',
            ],
            [
                'id' => 1000,
                'name' => 'alex',
                'email' => 'alex@alex.com',
                'password' => bcrypt('123456'),
                'email_verified_at' => Carbon::now(),
                'avatar' => 'avatarAngel.png',
            ],
            [
                'id' => 41,
                'name' => 'usuarioAmigo',
                'email' => 'userAmigo@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => Carbon::now(),
                'avatar' => 'avatarAngel.png',
            ],
            [
                'id' => 50,
                'name' => 'User 50',
                'email' => 'user50@example.com',
                'password' => bcrypt('password'),
                'avatar' => 'avatarAngel.png',
            ],
        ];
        foreach ($users as $userData) {
            $user = User::create($userData);
            $user->assignRole('user');
        }
    }
}
