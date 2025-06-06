<?php

namespace Database\Factories\Foro;

use App\Models\Foro\MensajeForo;
use App\Models\Foro\RespuestaForo;
use App\Models\users\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Foro\RespuestaForo>
 */
class RespuestaForoFactory extends Factory
{

    protected $model = RespuestaForo::class;

    public function definition()
    {
        return [
            'contenido' => $this->faker->paragraph(rand(1, 3)),
            'usuario_id' => User::factory(),
            'mensaje_id' => MensajeForo::factory(),
        ];
    }
}
