<?php

namespace Database\Factories\Forum;

use App\Models\Foro\Foro;
use App\Models\Foro\MensajeForo;
use App\Models\games\Videojuego;
use App\Models\users\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Foro\Foro>
 */class ForoFactory extends Factory
{
    protected $model = Foro::class;

    public function definition()
    {
        return [
            'titulo' => $this->faker->sentence(),
            'descripcion' => $this->faker->paragraph(),
            'imagen' => $this->faker->imageUrl(),
            'usuario_id' => User::factory(),
        ];
    }
    public function configure()
    {
        return $this->afterCreating(function (Foro $foro) {
            $videojuegos = Videojuego::inRandomOrder()->take(3)->pluck('id');
            $foro->videojuegos()->sync($videojuegos);

            MensajeForo::factory()->create([
                'foro_id' => $foro->id,
            ]);
        });
    }
}
