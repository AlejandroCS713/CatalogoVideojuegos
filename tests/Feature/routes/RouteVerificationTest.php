<?php

use App\Models\users\User;
use Illuminate\Support\Facades\Hash;

it('allows an authenticated user to access the email verification page', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->actingAs($user);
    $response = $this->get(route('verification.notice'));
    $response->assertStatus(200);
    $user->delete();
});

it('allows an authenticated user to request a verification email', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->actingAs($user);
    $response = $this->post(route('verification.send'));
    $response->assertSessionHas('message', 'Verification link sent!');
    $user->delete();
});
