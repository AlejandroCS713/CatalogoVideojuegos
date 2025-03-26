<?php

use App\Models\users\User;
use Illuminate\Support\Facades\Hash;

it('can access the foroum index page', function () {
    $response = $this->get(route('forum.index'));
    $response->assertStatus(200);
});

it('can access the forum show page', function () {
    $foro = \App\Models\Foro\Foro::factory()->create();
    $response = $this->get(route('forum.show', ['foro' => $foro->id]));
    $response->assertStatus(200);
});
it('allows registered users to access the forum creation page', function () {
    $user = \App\Models\users\User::factory()->create();
    $this->actingAs($user);
    $response = $this->get(route('forum.create'));
    $response->assertStatus(200);
    $user->delete();
});

it('allows a verified user to create a forum', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
    ]);
    $this->actingAs($user);
    $response = $this->get(route('forum.create'));
    $response->assertStatus(200);
    $user->delete();
});

