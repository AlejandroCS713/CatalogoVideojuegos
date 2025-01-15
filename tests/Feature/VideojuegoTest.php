<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Videojuego;

class VideojuegoTest extends TestCase
{
    public function test_crear_videojuego()
    {
        $videojuego = Videojuego::factory()->create([
            'nombre' => 'Test Game',
            'descripcion' => 'Juego de prueba',
        ]);

        $this->assertDatabaseHas('videojuegos', [
            'nombre' => 'Test Game',
        ]);
    }
    public function testNoSePuedeCrearVideojuegoSinNombre()
    {
        $this->expectException(QueryException::class);

        Videojuego::create([
            'descripcion' => 'Un videojuego sin nombre',
        ]);
    }

    public function testSePuedeCrearVideojuegoConSoloNombre()
    {
        $videojuego = Videojuego::create([
            'nombre' => 'Juego de prueba',
        ]);

        $this->assertDatabaseHas('videojuegos', [
            'nombre' => 'Juego de prueba',
        ]);
    }

}
