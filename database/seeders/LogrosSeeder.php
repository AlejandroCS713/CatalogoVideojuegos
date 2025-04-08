<?php

namespace Database\Seeders;

use App\Models\users\Logro;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LogrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $logro = Logro::firstOrCreate(
            [
                'nombre' => 'Primer Amigo',
            ],
            [
                'descripcion' => 'Has agregado tu primer amigo',
                'puntos' => 10,
            ]
        );

        Logro::firstOrCreate(
            ['nombre' => 'Primer Mensaje'],
            [
                'descripcion' => 'Has enviado o recibido tu primer mensaje con un amigo',
                'puntos' => 20,
            ]
        );
    }
}
