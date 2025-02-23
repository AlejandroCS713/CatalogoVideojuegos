<?php

use App\Models\games\Genero;
use App\Models\games\Plataforma;
use App\Models\games\Videojuego;
use App\Models\users\Friend;
use App\Models\users\Logro;
use App\Models\users\Message;
use App\Models\users\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;


it('can view the logros of the authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $logro1 = Logro::create([
        'user_id' => $user->id,
        'nombre' => 'Logro 1',
        'descripcion' => 'Descripción del Logro 1',
        'puntos' => 50
    ]);
    $logro2 = Logro::create([
        'user_id' => $user->id,
        'nombre' => 'Logro 2',
        'descripcion' => 'Descripción del Logro 2',
        'puntos' => 100
    ]);

    $response = $this->get(route('logros.perfil'));

    $response->assertStatus(200);

    $logro1->delete();
    $logro2->delete();

    $user->delete();
});

it('shows an empty logros view if no logros exist', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('logros.perfil'));

    $response->assertStatus(200);

    $user->delete();
});
it('allows user to send a friend request', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $this->be($user1);

    $response = $this->post(route('friends.send', $user2->id));

    $response->assertRedirect()->assertStatus(302);
    $this->assertDatabaseHas('friends', [
        'user_id' => $user1->id,
        'friend_id' => $user2->id,
        'status' => 'pending',
    ]);

    Friend::where('user_id', $user1->id)->where('friend_id', $user2->id)->delete();
    Friend::where('user_id', $user2->id)->where('friend_id', $user1->id)->delete();
    $user1->delete();
    $user2->delete();
});

it('allows user to accept a friend request', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Friend::create([
        'user_id' => $user1->id,
        'friend_id' => $user2->id,
        'status' => 'pending',
    ]);

    $this->be($user2);

    $response = $this->post(route('friends.accept', $user1->id));

    $response->assertRedirect()->assertStatus(302);
    $this->assertDatabaseHas('friends', [
        'user_id' => $user1->id,
        'friend_id' => $user2->id,
        'status' => 'accepted',
    ]);

    Friend::where('user_id', $user1->id)->where('friend_id', $user2->id)->delete();
    Friend::where('user_id', $user2->id)->where('friend_id', $user1->id)->delete();
    $user1->delete();
    $user2->delete();
});

it('allows user to remove a friend', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Friend::create([
        'user_id' => $user1->id,
        'friend_id' => $user2->id,
        'status' => 'accepted',
    ]);

    $this->be($user1);

    $response = $this->post(route('friends.remove', $user2->id));

    $response->assertRedirect()->assertStatus(302);
    $this->assertDatabaseMissing('friends', [
        'user_id' => $user1->id,
        'friend_id' => $user2->id,
    ]);

    $user1->delete();
    $user2->delete();
});


