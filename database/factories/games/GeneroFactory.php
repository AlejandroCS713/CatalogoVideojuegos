<?php

namespace Database\Factories\games;

use App\Models\games\Genero;
use Illuminate\Database\Eloquent\Factories\Factory;

class GeneroFactory extends Factory
{
    /**
     * El nombre del modelo que esta fábrica está generando.
     *
     * @var string
     */
    protected $model = Genero::class;

    /**
     * Definir el estado de los datos falsos para el modelo.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => $this->faker->word(),  // Genera una palabra aleatoria como nombre del género
        ];
    }
}
