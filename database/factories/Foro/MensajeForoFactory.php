<?php

namespace Database\Factories\Foro;

use App\Models\Foro\Foro;
use App\Models\Foro\MensajeForo;
use App\Models\users\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Foro\MensajeForo>
 */
class MensajeForoFactory extends Factory
{
    protected $model = MensajeForo::class;

    public function definition()
    {
        return [
            'usuario_id' => User::factory(),
            'foro_id' => Foro::factory(),
            'contenido' => $this->faker->paragraph()
        ];
    }
}
