<?php

namespace Tests\Feature\Controllers;

use App\Models\users\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    if (!Role::where('name', 'user')->exists()) {
        Role::create(['name' => 'user']);
    }

    if (!Role::where('name', 'admin')->exists()) {
        Role::create(['name' => 'admin']);
    }
});

it('user with role user can access profile', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk()
        ->assertViewHasAll(['user', 'friends']);

    $user->delete();
});

it('non user role cannot access profile', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get(route('profile'))
        ->assertForbidden();

    $admin->delete();
});

it('edit avatar returns avatar view', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)
        ->get(route('profile.avatar'))
        ->assertOk()
        ->assertViewIs('profile.avatar')
        ->assertViewHas('avatars');

    $user->delete();
});

it('update avatar changes user avatar', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)
        ->post(route('profile.avatar.update'), ['avatar' => 'avatarPro.png'])
        ->assertRedirect(route('profile'))
        ->assertSessionHas('success');

    expect($user->fresh()->avatar)->toBe('avatarPro.png');

    $user->delete();
});



