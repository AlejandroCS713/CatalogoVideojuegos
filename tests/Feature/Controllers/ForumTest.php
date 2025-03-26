<?php

use App\Models\Foro\Foro;
use App\Models\Foro\MensajeForo;
use App\Models\games\Videojuego;
use App\Models\users\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

it('allows user to view forum index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Auth::login($admin);

    $response = $this->get(route('forum.index'));

    $response->assertStatus(200);
    $response->assertViewIs('forum.index');
    $response->assertViewHas('foros');

    $admin->delete();
});

it('allows user to view a specific forum', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Auth::login($admin);

    $foro = Foro::factory()->create();

    $response = $this->get(route('forum.show', $foro->id));

    $response->assertStatus(200);
    $response->assertViewIs('forum.show');
    $response->assertViewHas('foro');

    $admin->delete();
    $foro->delete();
});

it('allows user to access create forum page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Auth::login($admin);

    $response = $this->get(route('forum.create'));

    $response->assertStatus(200);
    $response->assertViewIs('forum.create');

    $admin->delete();
});


it('allows user to access edit forum page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Auth::login($admin);

    $foro = Foro::factory()->create();

    $response = $this->get(route('forum.edit', $foro->id));

    $response->assertStatus(200);
    $response->assertViewIs('forum.edit');
    $response->assertViewHas('foro');

    $admin->delete();
    $foro->delete();
});


it('allows user to delete a forum', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Auth::login($admin);

    $foro = Foro::factory()->create();

    $response = $this->delete(route('forum.destroy', $foro->id));

    $response->assertRedirect(route('forum.index'));
    $this->assertDatabaseMissing('foros', ['id' => $foro->id]);

    $admin->delete();
    $foro->delete();
});


it('allows user to store a new forum', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Auth::login($admin);

    $videojuego = Videojuego::factory()->create();

    $imagen = UploadedFile::fake()->image('foro-imagen.jpg');

    $requestData = [
        'titulo' => 'Nuevo Foro',
        'descripcion' => 'Descripción del foro.',
        'imagen' => $imagen,
        'videojuego_id' => $videojuego->id,
    ];

    $response = $this->post(route('forum.store'), $requestData);

    $response->assertRedirect(route('forum.index'));
    $this->assertDatabaseHas('foros', ['titulo' => 'Nuevo Foro']);

    $admin->delete();
    $videojuego->delete();
    Foro::where('titulo', 'Nuevo Foro')->delete();
});


it('allows user to update forum', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Auth::login($admin);

    $foro = Foro::factory()->create();

    $imagen = UploadedFile::fake()->image('foro-imagen.jpg');

    $requestData = [
        'titulo' => 'Foro Actualizado',
        'descripcion' => 'Nueva descripción',
        'imagen' => $imagen,
    ];

    $response = $this->put(route('forum.update', $foro->id), $requestData);

    $response->assertRedirect(route('forum.index'));
    $this->assertDatabaseHas('foros', ['titulo' => 'Foro Actualizado']);

    $admin->delete();
    $foro->delete();
});

it('generates PDF for a forum', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Auth::login($admin);

    $foro = Foro::factory()->create();

    $response = $this->get(route('forum.pdf', $foro->id));

    $response->assertStatus(200);
    $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));

    $admin->delete();
    $foro->delete();
});

it('allows user to store a new mensaje in a foro', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $foro = Foro::factory()->create();

    $imagen = UploadedFile::fake()->image('mensaje-imagen.jpg');

    $requestData = [
        'contenido' => 'Este es un mensaje en el foro.',
        'imagen' => $imagen,
        'foro_id' => $foro->id,
    ];

    $response = $this->post(route('mensajes.store', ['foro' => $foro->id]), $requestData);

    $response->assertRedirect(route('forum.show', $foro->id));
    $this->assertDatabaseHas('mensaje_foros', ['contenido' => 'Este es un mensaje en el foro.']);

    $user->delete();
    $foro->delete();
});

it('does not allow user to store mensaje with empty content', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $foro = Foro::factory()->create();

    $requestData = [
        'contenido' => '',
        'foro_id' => $foro->id,
    ];

    $response = $this->post(route('mensajes.store', ['foro' => $foro->id]), $requestData);

    $response->assertSessionHasErrors('contenido');

    $user->delete();
    $foro->delete();
});

it('allows user to store a new respuesta in a mensaje', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $foro = Foro::factory()->create();
    $mensaje = MensajeForo::factory()->create(['foro_id' => $foro->id]);

    $imagen = UploadedFile::fake()->image('respuesta-imagen.jpg');

    $requestData = [
        'contenido' => 'Esta es una respuesta al mensaje.',
        'imagen' => $imagen,
        'mensaje_id' => $mensaje->id,
    ];

    $response = $this->post(route('respuestas.store', ['mensaje' => $mensaje->id]), $requestData);

    $response->assertRedirect(route('forum.show', $foro->id));
    $this->assertDatabaseHas('respuesta_foros', ['contenido' => 'Esta es una respuesta al mensaje.']);

    $user->delete();
    $foro->delete();
    $mensaje->delete();
});

it('does not allow user to store respuesta with empty content', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $foro = Foro::factory()->create();
    $mensaje = MensajeForo::factory()->create(['foro_id' => $foro->id]);

    $requestData = [
        'contenido' => '',
        'mensaje_id' => $mensaje->id,
    ];

    $response = $this->post(route('respuestas.store', ['mensaje' => $mensaje->id]), $requestData);

    $response->assertSessionHasErrors('contenido');

    $user->delete();
    $foro->delete();
    $mensaje->delete();
});
