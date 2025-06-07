<?php

namespace Tests\Feature\Controllers;

use App\Events\PerfilActualizado;
use App\Models\users\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    if (!Role::where('name', 'user')->exists()) {
        Role::create(['name' => 'user']);
    }

    if (!Role::where('name', 'admin')->exists()) {
        Role::create(['name' => 'admin']);
    }
        Event::fake();
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

it('returns the unified profile edit view with avatars', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)
        ->get(route('profile.avatar'))
        ->assertOk()
        ->assertViewIs('profile.avatar')
        ->assertViewHas('avatars')
        ->assertViewHas('user');
});


it('updates user avatar and dispatches event', function () {
    $user = User::factory()->create(['avatar' => 'avatarAngel.png']);
    $user->assignRole('user');

    $newAvatar = 'avatarPro.png';

    $this->actingAs($user)
        ->put(route('profile.update-avatar'), ['avatar' => $newAvatar])
        ->assertRedirect(route('profile.avatar'))
        ->assertSessionHas('success');

    $user->refresh();
    expect($user->avatar)->toBe($newAvatar);

    Event::assertDispatched(PerfilActualizado::class, function ($event) use ($user) {
        return $event->user->is($user) && in_array('avatar', $event->cambiosRealizados);
    });
});


it('updates user nickname and dispatches event', function () {
    $user = User::factory()->create(['name' => 'OldNick']);
    $user->assignRole('user');

    $newNickname = 'NewNick';

    $this->actingAs($user)
        ->put(route('profile.update'), ['name' => $newNickname])
        ->assertRedirect(route('profile.avatar'))
        ->assertSessionHas('success');

    $user->refresh();
    expect($user->name)->toBe($newNickname);

    Event::assertDispatched(PerfilActualizado::class, function ($event) use ($user) {
        return $event->user->is($user) && in_array('name', $event->cambiosRealizados);
    });
});

it('updates user password and dispatches event', function () {
    $user = User::factory()->create(['password' => Hash::make('old_password')]);
    $user->assignRole('user');

    $newPassword = 'new_strong_password';

    $this->actingAs($user)
        ->put(route('profile.update'), [
            'password' => $newPassword,
            'password_confirmation' => $newPassword
        ])
        ->assertRedirect(route('profile.avatar'))
        ->assertSessionHas('success');

    $user->refresh();
    expect(Hash::check($newPassword, $user->password))->toBeTrue();

    Event::assertDispatched(PerfilActualizado::class, function ($event) use ($user) {
        return $event->user->is($user) && in_array('password', $event->cambiosRealizados);
    });
});

it('updates both nickname and password and dispatches event', function () {
    $user = User::factory()->create(['name' => 'OldName', 'password' => Hash::make('old_pass')]);
    $user->assignRole('user');

    $newNickname = 'CombinedNick';
    $newPassword = 'super_new_password';

    $this->actingAs($user)
        ->put(route('profile.update'), [
            'name' => $newNickname,
            'password' => $newPassword,
            'password_confirmation' => $newPassword
        ])
        ->assertRedirect(route('profile.avatar'))
        ->assertSessionHas('success');

    $user->refresh();
    expect($user->name)->toBe($newNickname);
    expect(Hash::check($newPassword, $user->password))->toBeTrue();

    Event::assertDispatched(PerfilActualizado::class, function ($event) use ($user) {
        return $event->user->is($user) &&
            in_array('name', $event->cambiosRealizados) &&
            in_array('password', $event->cambiosRealizados);
    });
});

it('does not update profile if no changes are made', function () {
    $user = User::factory()->create(['name' => 'CurrentNick']);
    $user->assignRole('user');

    $this->actingAs($user)
        ->put(route('profile.update'), ['name' => 'CurrentNick'])
        ->assertRedirect(route('profile.avatar'))
        ->assertSessionHas('info');

    $user->refresh();
    expect($user->name)->toBe('CurrentNick');

    Event::assertNotDispatched(PerfilActualizado::class);
});



