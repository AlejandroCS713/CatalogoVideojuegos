<?php

namespace Tests\Feature;

use App\Models\games\Genero;
use App\Models\games\Plataforma;
use App\Models\games\Videojuego;
use Illuminate\Database\QueryException;
use Tests\TestCase;

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

    public function test_no_se_puede_crear_videojuego_con_nombre_duplicado()
    {
        Videojuego::factory()->create(['nombre' => 'Juego Único']);

        $response = $this->post('/videojuegos', ['nombre' => 'Juego Único', 'descripcion' => 'Descripción duplicada']);

        $response->assertSessionHasErrors(['nombre']);
    }

    public function test_videojuego_se_asocia_a_generos()
    {
        $genero = Genero::factory()->create(['nombre' => 'Aventura']);
        $videojuego = Videojuego::factory()->create();

        $videojuego->generos()->attach($genero);

        $this->assertTrue($videojuego->generos->contains($genero));
    }
    public function test_nombre_no_debe_exceder_longitud_maxima()
    {
        $nombreLargo = str_repeat('a', 256); // Suponiendo que el límite es 255 caracteres
        $response = $this->post('/videojuegos', ['nombre' => $nombreLargo, 'descripcion' => 'Descripción válida']);

        $response->assertSessionHasErrors(['nombre']);
    }


    public function test_no_se_puede_crear_videojuego_con_datos_invalidos()
    {
        $response = $this->post('/videojuegos', ['nombre' => 12345, 'descripcion' => true]);

        $response->assertSessionHasErrors(['nombre', 'descripcion']);
    }

    public function test_videojuego_se_asocia_a_plataformas()
    {
        $plataforma = Plataforma::factory()->create(); // Creamos una plataforma
        $videojuego = Videojuego::factory()->create(); // Creamos un videojuego

        // Asociamos la plataforma al videojuego (si la relación es muchos a muchos)
        $videojuego->plataformas()->attach($plataforma);

        // Comprobamos que el videojuego tiene asociada la plataforma
        $this->assertTrue($videojuego->plataformas->contains($plataforma));
    }

}
