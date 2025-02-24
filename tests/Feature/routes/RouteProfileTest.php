<?php

use App\Models\users\User;
use Illuminate\Support\Facades\Hash;

it('allows an authenticated user to access the profile page', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->actingAs($user);
    $response = $this->get(route('profile'));
    $response->assertStatus(200);
    $user->delete();
    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
});


it('allows an authenticated user to access the avatar edit page', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->actingAs($user);
    $response = $this->get(route('profile.avatar'));
    $response->assertStatus(200);
    $user->delete();
    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
});

it('does not allow a user who is not verified to access the profile page', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => null,
    ]);
    $this->actingAs($user);
    $response = $this->get(route('profile'));
    $response->assertRedirect(route('verification.notice'));
    $user->delete();
});
it('allows a verified user to access the profile page', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
    ]);
    $this->actingAs($user);
    $response = $this->get(route('profile'));
    $response->assertStatus(200);
    $user->delete();
});

