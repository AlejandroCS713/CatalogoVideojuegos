<?php

namespace Database\Factories\Foro;

use App\Models\Foro\Foro;
use App\Models\Foro\MensajeForo;
use App\Models\games\Videojuego;
use App\Models\users\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ForoFactory extends Factory
{
    protected $model = Foro::class;

    public function definition(): array
    {
        return [
            'titulo' => $this->faker->sentence(),
            'descripcion' => $this->faker->paragraph(),
            'usuario_id' => User::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Foro $foro) {
            $videojuegos = Videojuego::inRandomOrder()->take(3)->pluck('id');

            $roles = ['principal', 'secundario', 'opcional'];

            $syncData = [];
            foreach ($videojuegos as $i => $id) {
                $syncData[$id] = ['rol' => $roles[$i] ?? 'principal'];
            }

            $foro->videojuegos()->sync($syncData);

        });
    }

    public function withInitialMessage(): static
    {
        return $this->afterCreating(function (Foro $foro) {
            MensajeForo::factory()->create([
                'foro_id' => $foro->id,
            ]);
        });
    }
}
