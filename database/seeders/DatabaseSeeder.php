<?php

namespace Database\Seeders;

use App\Models\users\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionsSeeder::class,
            GamesTableSeeder::class,
            UsersTableSeeder::class,
            ForumTableSeeder::class,
        ]);
    }
}
