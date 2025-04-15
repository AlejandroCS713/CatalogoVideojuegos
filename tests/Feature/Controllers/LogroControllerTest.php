<?php

namespace Tests\Feature\Controllers\Users;

use App\Models\users\Logro;
use App\Models\users\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Permission\Models\Role;

uses();

describe('LogroController', function () {
    it('shows the user\'s logros', function () {
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $user = User::factory()->create();
        $user->assignRole($userRole);

        $logro1 = Logro::factory()->create();
        $logro2 = Logro::factory()->create();

        $user->logros()->attach([$logro1->id, $logro2->id]);

        $this->actingAs($user);
        $response = $this->get(route('logros.perfil'));

        $response->assertStatus(200)
            ->assertViewIs('profile.logros')
            ->assertViewHas('logros')
            ->assertViewHas('logros', $user->logros->fresh());

        $logro1->delete();
        $logro2->delete();
        $user->delete();
    });

    it('redirects to login if user is not authenticated for index', function () {
        $response = $this->get('/profile/logros');
        $response->assertRedirect(route('login'));
    });

    it('redirects to login if user is not authenticated for generarPDF', function () {
        $response = $this->get('/mis-logros/pdf');
        $response->assertRedirect(route('login'));
    });

    it('downloads a PDF with the user\'s logros', function () {
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $user = User::factory()->create();
        $user->assignRole($userRole);

        $logro1 = Logro::factory()->create();
        $logro2 = Logro::factory()->create();
        $user->logros()->attach([$logro1->id, $logro2->id]);

        $this->actingAs($user);

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('profile.logros-pdf', [
                'user' => $user,
                'logros' => $user->logros,
            ])
            ->andReturnSelf();

        Pdf::shouldReceive('download')
            ->once()
            ->with("Logros-{$user->id}.pdf")
            ->andReturn(response('Contenido PDF simulado', 200, [
                'Content-Type' => 'application/pdf',
            ]));

        $response = $this->get('/mis-logros/pdf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertSee('Contenido PDF simulado');

        $user->logros()->detach();
        $logro1->delete();
        $logro2->delete();
        $user->delete();
    });
});
