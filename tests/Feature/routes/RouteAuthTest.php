<?php

use App\Models\users\User;
use Illuminate\Support\Facades\Hash;

it('can access the login page', function () {
    $response = $this->get('login');
    $response->assertStatus(200);
});
it('can access the registered page', function () {
    $response = $this->get('register');
    $response->assertStatus(200);
});


it('allows a user to log in', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);
    $response->assertRedirect(route('welcome'));
    $this->assertAuthenticatedAs($user);
    $user->delete();
    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
});

it('allows a user to register', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];
    $response = $this->post('/register', $userData);
    $response->assertRedirect(route('verification.notice'));
    $this->assertDatabaseHas('users', ['email' => $userData['email']]);
    \App\Models\users\User::where('email', $userData['email'])->delete();
    $this->assertDatabaseMissing('users', ['email' => $userData['email']]);
});


it('allows a user to log out', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);
    $this->assertAuthenticatedAs($user);
    $response = $this->post(route('logout'));
    $this->assertGuest();
    $response->assertRedirect('/');
    $user->delete();
    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
});

