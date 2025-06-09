<?php

namespace Database\Factories\games;

use App\Models\games\Plataforma;
use App\Models\games\Videojuego;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\games\Plataforma>
 */
class PlataformaFactory extends Factory
{
    protected $model = Plataforma::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->unique()->word,
        ];
    }
    public function withVideojuegos()
    {
        return $this->has(Videojuego::factory()->count(3));
    }
}
