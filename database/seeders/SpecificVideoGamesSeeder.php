<?php

namespace Database\Seeders;

use App\Models\games\Videojuego;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class SpecificVideoGamesSeeder extends Seeder
{
    public function run(): void
    {
        $game1 = Videojuego::firstOrCreate(
            ['id' => 1],
            [
                'nombre' => 'The Legend of Zelda: Breath of the Wild',
                'descripcion' => 'Un vasto mundo abierto lleno de misterios y aventuras.',
                'fecha_lanzamiento' => '2017-03-03',
                'rating_usuario' => 95,
                'rating_criticas' => 97,
                'desarrollador' => 'Nintendo',
                'publicador' => 'Nintendo',
            ]
        );

        if ($game1->wasRecentlyCreated) {
            Log::info('Videojuego 1 "The Legend of Zelda: Breath of the Wild" creado.');
        } else {
            Log::info('Videojuego 1 "The Legend of Zelda: Breath of the Wild" ya existía.');
        }

        $game2 = Videojuego::firstOrCreate(
            ['id' => 5],
            [
                'nombre' => 'Elden Ring',
                'descripcion' => 'Un RPG de acción de fantasía épica, con un vasto mundo y combates desafiantes.',
                'fecha_lanzamiento' => '2022-02-25',
                'rating_usuario' => 93,
                'rating_criticas' => 96,
                'desarrollador' => 'FromSoftware',
                'publicador' => 'Bandai Namco Entertainment',
            ]
        );

        if ($game2->wasRecentlyCreated) {
            Log::info('Videojuego 2 "Elden Ring" creado.');
        } else {
            Log::info('Videojuego 2 "Elden Ring" ya existía.');
        }

        $this->command->info('Seed completado para videojuegos específicos.');
    }
}
