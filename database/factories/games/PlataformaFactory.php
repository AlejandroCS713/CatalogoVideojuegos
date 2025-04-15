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
    // Definir el modelo que usa este factory
    protected $model = Plataforma::class;

    // Definir los valores por defecto que se usarán para crear plataformas
    public function definition()
    {
        return [
            'nombre' => $this->faker->unique()->word, // Nombre único para la plataforma
        ];
    }
    public function withVideojuegos()
    {
        return $this->has(Videojuego::factory()->count(3)); // Asociamos 3 videojuegos a esta plataforma
    }
}
