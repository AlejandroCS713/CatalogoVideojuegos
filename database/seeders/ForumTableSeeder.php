<?php

namespace Database\Seeders;

use App\Models\Foro\Foro;
use App\Models\Foro\MensajeForo;
use App\Models\Foro\RespuestaForo;
use App\Models\games\Videojuego;
use App\Models\users\User;
use Illuminate\Database\Seeder;


class ForumTableSeeder extends Seeder
{
    public function run()
    {
        $user = User::find(40);

        if (!$user) {
            $this->command->error("El usuario con ID 40 no existe.");
            return;
        }

        $videojuegos = Videojuego::inRandomOrder()->take(3)->get();

        foreach ($videojuegos as $videojuego) {
            $foro = Foro::create([
                'titulo' => 'Discutir sobre ' . $videojuego->nombre,
                'descripcion' => 'Este foro es para hablar sobre el videojuego ' . $videojuego->nombre . '.',
                'usuario_id' => $user->id,
            ]);

            $foro->videojuegos()->attach($videojuego->id);

            $mensaje1 = MensajeForo::create([
                'contenido' => 'Me encanta este juego, ¿alguien más lo ha jugado?',
                'foro_id' => $foro->id,
                'usuario_id' => $user->id,
            ]);

            $mensaje2 = MensajeForo::create([
                'contenido' => 'Sí, lo jugué el mes pasado. Es increíble la historia.',
                'foro_id' => $foro->id,
                'usuario_id' => $user->id,
            ]);

            RespuestaForo::create([
                'contenido' => 'A mí también me gustó mucho el final. Pero me hubiera gustado más si...',
                'mensaje_id' => $mensaje1->id,
                'usuario_id' => $user->id,
            ]);

            RespuestaForo::create([
                'contenido' => '¡Totalmente de acuerdo! Me hizo llorar.',
                'mensaje_id' => $mensaje2->id,
                'usuario_id' => $user->id,
            ]);
        }

        $this->command->info('Foros, mensajes y respuestas creados exitosamente!');
    }
}
