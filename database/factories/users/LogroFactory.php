<?php

namespace Database\Factories\users;

use App\Models\users\Logro;
use App\Models\users\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogroFactory extends Factory
{
    protected $model = Logro::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraph,
            'puntos' => $this->faker->numberBetween(10, 100),
        ];
    }
}
