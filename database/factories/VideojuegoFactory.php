<?php

namespace Database\Factories;

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
            'nombre' => $this->faker->sentence(3), // Nombre ficticio
            'descripcion' => $this->faker->paragraph(), // Descripción ficticia
            'fecha_lanzamiento' => $this->faker->date(), // Fecha aleatoria
            'rating_usuario' => $this->faker->numberBetween(0, 100), // Rating usuario entre 0 y 100
            'rating_criticas' => $this->faker->numberBetween(0, 100), // Rating críticas entre 0 y 100
            'desarrollador' => $this->faker->company(), // Nombre de desarrollador ficticio
            'publicador' => $this->faker->company(), // Nombre de publicador ficticio
        ];
    }
}

