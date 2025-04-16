<?php

use App\Livewire\FriendList;
use App\Models\users\Friend;
use App\Models\users\User;
use Livewire\Livewire;

it('loads friends correctly', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();

    Friend::create([
        'user_id' => $user->id,
        'friend_id' => $friend->id,
        'status' => 'accepted',
    ]);

    Livewire::actingAs($user)
        ->test(FriendList::class)
        ->assertSee($friend->name);
});

it('removes a friend and redirects to profile', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create();

    Friend::create([
        'user_id' => $user->id,
        'friend_id' => $friend->id,
        'status' => 'accepted',
    ]);

    Livewire::actingAs($user)
        ->test(FriendList::class)
        ->call('removeFriend', $friend->id)
        ->assertRedirect(route('profile'));
});
