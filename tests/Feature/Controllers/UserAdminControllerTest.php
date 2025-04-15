<?php

use App\Models\users\User;
use Illuminate\Support\Facades\Artisan;

it('admin can access the dashboard', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewIs('admin.dashboard');
});

it('non-admin cannot access the dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('admin.dashboard'));

    $response->assertStatus(403);
});
it('admin can send bulk email', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)
        ->post(route('send.bulk.email'), [
            'message' => 'Este es un mensaje de prueba para el envío masivo de correos.'
        ]);

    $response->assertRedirect(route('admin.dashboard'))
        ->assertSessionHas('success', 'El comando para encolar correos se ha ejecutado. Los correos se enviarán en segundo plano.');
});

it('admin receives error when bulk email fails', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Artisan::shouldReceive('call')
        ->with('bulk:email', ['message' => 'mensaje de prueba'])
        ->andThrow(new \Exception('Error en el comando'));

    $response = $this->actingAs($admin)
        ->post(route('send.bulk.email'), [
            'message' => 'Este es un mensaje de prueba para el envío masivo de correos.'
        ]);

    $response->assertRedirect(route('admin.dashboard'))
        ->assertSessionHas('error', 'Ocurrió un error inesperado al intentar iniciar el comando de envío. Revisa los logs.');
});
