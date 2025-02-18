<?php

namespace Database\Factories\games;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\games\Videojuego>
 */
class VideojuegoFactory extends Factory
{
    protected $model = \App\Models\games\Videojuego::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraph(),
            'fecha_lanzamiento' => $this->faker->date(),
            'rating_usuario' => $this->faker->numberBetween(0, 100),
            'rating_criticas' => $this->faker->numberBetween(0, 100),
            'desarrollador' => $this->faker->company(),
            'publicador' => $this->faker->company(),
        ];
    }
}

