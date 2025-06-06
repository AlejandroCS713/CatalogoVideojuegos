<?php

namespace Tests\Feature\Controllers;

use App\Models\Foro\Foro;
use App\Models\Foro\MensajeForo;
use App\Models\Foro\RespuestaForo;
use App\Models\users\User;
use App\Models\games\Videojuego;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'user']);
});

it('displays a paginated list of forums', function () {
    Foro::factory()->count(12)->create();

    $response = $this->get('/foro');

    $response->assertOk()
        ->assertViewIs('foro.index')
        ->assertViewHas('foros', function ($foros) {
            return $foros->count() === 10
                && $foros instanceof \Illuminate\Pagination\LengthAwarePaginator;
        });
});

it('displays an empty list when no forums exist', function () {
    $response = $this->get('/foro');

    $response->assertOk()
        ->assertViewIs('foro.index')
        ->assertViewHas('foros', function ($foros) {
            return $foros->isEmpty();
        });
});

it('displays a specific forum with its messages, responses, and associated videogames', function () {
    $foro = Foro::factory()->create();
    $user = User::factory()->create();
    $videojuego = Videojuego::factory()->create();

    $foro->videojuegos()->attach($videojuego);

    $mensaje = MensajeForo::factory()->create(['foro_id' => $foro->id, 'usuario_id' => $user->id]);

    RespuestaForo::factory()->count(2)->create(['mensaje_id' => $mensaje->id, 'usuario_id' => $user->id]);

    $response = $this->get("/foro/{$foro->id}");

    $response->assertOk()
        ->assertViewIs('foro.show')
        ->assertViewHas('foro', function ($viewForo) use ($foro) {
            return $viewForo->id === $foro->id &&
                $viewForo->relationLoaded('mensajes') &&
                $viewForo->mensajes->first()->relationLoaded('usuario') &&
                $viewForo->mensajes->first()->relationLoaded('respuestas') &&
                $viewForo->mensajes->first()->respuestas->first()->relationLoaded('usuario') &&
                $viewForo->relationLoaded('videojuegos') &&
                $viewForo->videojuegos->isNotEmpty();
        });
});

it('returns 404 when displaying a non-existent forum', function () {
    $this->get('/foro/9999')->assertNotFound();
});

it('generates and downloads a PDF for a specific forum', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('user');
    Sanctum::actingAs($user);

    $foro = Foro::factory()->create();
    $foro->usuario()->associate($user)->save();

    $expectedFilename = "Foro-" . Str::slug($user->name) . ".pdf";

    Pdf::shouldReceive('loadView')
        ->once()
        ->with('foro.pdf', \Mockery::on(function ($data) use ($foro) {
            return $data['foro']->id === $foro->id;
        }))
        ->andReturnSelf();

    Pdf::shouldReceive('download')
        ->once()
        ->with($expectedFilename)
        ->andReturnUsing(function ($filename) {
            return response()->make('PDF_Content', 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        });

    $response = $this->get("/foro/{$foro->id}/pdf");

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('Content-Disposition', 'attachment; filename="' . $expectedFilename . '"');
});

it('returns 404 when generating a PDF for a non-existent forum', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('user');
    Sanctum::actingAs($user);

    $this->get('/foro/9999/pdf')->assertNotFound();
});
