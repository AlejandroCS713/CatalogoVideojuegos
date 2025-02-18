<?php

namespace Database\Factories\Forum;

use App\Models\Forum\Foro;
use App\Models\Forum\MensajeForo;
use App\Models\users\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Forum\MensajeForo>
 */
class MensajeForoFactory extends Factory
{
    protected $model = MensajeForo::class;

    public function definition()
    {
        return [
            'usuario_id' => User::factory(),
            'foro_id' => Foro::factory(),
            'contenido' => $this->faker->paragraph(),
            'imagen' => $this->faker->optional()->imageUrl()
        ];
    }
}
