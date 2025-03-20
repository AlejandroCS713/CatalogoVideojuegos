<?php

use App\Models\games\Genero;
use App\Models\games\Plataforma;
use App\Models\games\Videojuego;
use App\Models\users\Friend;
use App\Models\users\Logro;
use App\Models\users\Message;
use App\Models\users\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;


it('can view the logros of the authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $logro1 = Logro::create([
        'user_id' => $user->id,
        'nombre' => 'Logro 1',
        'descripcion' => 'Descripción del Logro 1',
        'puntos' => 50
    ]);
    $logro2 = Logro::create([
        'user_id' => $user->id,
        'nombre' => 'Logro 2',
        'descripcion' => 'Descripción del Logro 2',
        'puntos' => 100
    ]);

    $response = $this->get(route('logros.perfil'));

    $response->assertStatus(200);

    $logro1->delete();
    $logro2->delete();

    $user->delete();
});

it('shows an empty logros view if no logros exist', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('logros.perfil'));

    $response->assertStatus(200);

    $user->delete();
});

it('allows user to remove a friend', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Friend::create([
        'user_id' => $user1->id,
        'friend_id' => $user2->id,
        'status' => 'accepted',
    ]);

    $this->be($user1);

    $response = $this->post(route('friends.remove', $user2->id));

    $response->assertRedirect()->assertStatus(302);
    $this->assertDatabaseMissing('friends', [
        'user_id' => $user1->id,
        'friend_id' => $user2->id,
    ]);

    $user1->delete();
    $user2->delete();
});

beforeEach(function () {
    $this->adminUser = User::where('email', 'admin@admin.com')->first();

    $this->assertNotNull($this->adminUser, 'El usuario admin no fue encontrado en la base de datos.');

    $this->actingAs($this->adminUser);
});

it('verifica que se puede acceder a la vista de creación de un videojuego', function () {
    $response = $this->get(route('admin.create'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.create');
    $response->assertViewHas('plataformas');
    $response->assertViewHas('generos');
});

it('verifica que se puede acceder a la vista de edición de un videojuego', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Videojuego para editar',
        'descripcion' => 'Descripción',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 80,
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);

    $response = $this->get(route('admin.edit', ['id' => $videojuego->id]));

    $response->assertStatus(200);
    $response->assertViewIs('admin.edit');
    $response->assertViewHas('videojuego');
    $response->assertViewHas('plataformas');
    $response->assertViewHas('generos');

    $videojuego->delete();
});


it('verifica que se puede eliminar un videojuego correctamente', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Videojuego para eliminar',
        'descripcion' => 'Descripción',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 8.5,  // Cambiado a un valor válido
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);
    $plataforma = Plataforma::create(['nombre' => 'PC']);
    $genero = Genero::create(['nombre' => 'Acción']);

    $videojuego->plataformas()->attach($plataforma->id);
    $videojuego->generos()->attach($genero->id);

    $response = $this->delete(route('admin.destroy', ['id' => $videojuego->id]));

    $response->assertRedirect(route('videojuegos.index'));
    $response->assertSessionHas('success', 'Videojuego eliminado.');

    $this->assertDatabaseMissing('videojuegos', ['nombre' => 'Videojuego para eliminar']);
    $this->assertDatabaseMissing('videojuego_plataforma', ['plataforma_id' => $plataforma->id]);
    $this->assertDatabaseMissing('videojuego_genero', ['genero_id' => $genero->id]); // Cambié a videojuego_genero

    // Eliminar los datos creados
    Plataforma::find($plataforma->id)->delete();
    Genero::find($genero->id)->delete();
});
it('verifica que se puede actualizar un videojuego correctamente', function () {
    $videojuego = Videojuego::create([
        'nombre' => 'Videojuego para actualizar',
        'descripcion' => 'Descripción',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 8.5,  // Cambiado a un valor válido
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test'
    ]);
    $plataforma = Plataforma::create(['nombre' => 'PC']);
    $genero = Genero::create(['nombre' => 'Acción']);

    $videojuego->plataformas()->attach($plataforma->id);
    $videojuego->generos()->attach($genero->id);

    $data = [
        'nombre' => 'Videojuego actualizado - ' . uniqid(), // Cambié el nombre para que sea único
        'descripcion' => 'Descripción actualizada',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 5.0,
        'rating_criticas' => 8.5,  // Cambiado a un valor válido
        'desarrollador' => 'Nuevo Desarrollador',
        'publicador' => 'Nuevo Publicador',
        'plataformas' => [$plataforma->id],
        'generos' => [$genero->id],
    ];

    $response = $this->put(route('admin.update', ['id' => $videojuego->id]), $data);

    $response->assertRedirect(route('videojuegos.index'));
    $response->assertSessionHas('success', 'Videojuego actualizado.');

    $this->assertDatabaseHas('videojuegos', ['nombre' => $data['nombre']]);
    $this->assertDatabaseHas('videojuego_plataforma', ['plataforma_id' => $plataforma->id]);
    $this->assertDatabaseHas('videojuego_genero', ['genero_id' => $genero->id]); // Cambié a videojuego_genero

    // Eliminar los datos creados
    $videojuego->delete();
    Plataforma::find($plataforma->id)->delete();
    Genero::find($genero->id)->delete();
});

it('verifica que se puede almacenar un nuevo videojuego correctamente', function () {
    $plataforma = Plataforma::create(['nombre' => 'PC']);
    $genero = Genero::create(['nombre' => 'Acción']);

    $data = [
        'nombre' => 'Nuevo Videojuego - ' . uniqid(), // Cambié el nombre para que sea único
        'descripcion' => 'Descripción del juego',
        'fecha_lanzamiento' => now(),
        'rating_usuario' => 4.5,
        'rating_criticas' => 8.5,  // Cambiado a un valor válido
        'desarrollador' => 'Desarrollador Test',
        'publicador' => 'Publicador Test',
        'plataformas' => [$plataforma->id],
        'generos' => [$genero->id],
    ];

    $response = $this->post(route('admin.store'), $data);

    $response->assertRedirect(route('videojuegos.index'));
    $response->assertSessionHas('success', 'Videojuego creado exitosamente.');

    $this->assertDatabaseHas('videojuegos', ['nombre' => $data['nombre']]);
    $this->assertDatabaseHas('videojuego_plataforma', ['plataforma_id' => $plataforma->id]);
    $this->assertDatabaseHas('videojuego_genero', ['genero_id' => $genero->id]); // Cambié a videojuego_genero

    // Eliminar los datos creados
    Plataforma::find($plataforma->id)->delete();
    Genero::find($genero->id)->delete();
    Videojuego::where('nombre', $data['nombre'])->delete();
});
