<?php

namespace Database\Factories\games;

use App\Models\games\Multimedia;
use App\Models\games\Videojuego;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MultimediaFactory extends Factory
{

    protected $model = Multimedia::class;

    public function definition(): array
    {
        $fileName = Str::random(12) . '.jpg';
        $filePath = 'storage/videojuegos/' . $fileName;

        return [
            'videojuego_id' => Videojuego::factory(),
            'tipo' => 'imagen',
            'url' => $filePath,
        ];
    }

    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'video',
            'url' => 'storage/videojuegos/videos/' . Str::random(12) . '.mp4',
        ]);
    }

    public function external(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'imagen',
            'url' => $this->faker->imageUrl(),
        ]);
    }
}
