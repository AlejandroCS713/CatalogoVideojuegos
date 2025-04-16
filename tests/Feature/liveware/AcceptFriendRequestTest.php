<?php

use App\Livewire\AcceptFriendRequests;
use App\Models\users\Friend;
use App\Events\AmigoAgregado;
use App\Models\users\User;
use Livewire\Livewire;
use Illuminate\Support\Facades\Event;

describe('AcceptFriendRequests Component', function () {

    it('renders and loads friend requests on mount', function () {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        Friend::create([
            'user_id' => $sender->id,
            'friend_id' => $user->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($user)
            ->test(AcceptFriendRequests::class)
            ->assertSee($sender->name);
    });

    it('accepts a friend request and dispatches events', function () {
        Event::fake();

        $receiver = User::factory()->create();
        $sender = User::factory()->create();

        Friend::create([
            'user_id' => $sender->id,
            'friend_id' => $receiver->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($receiver)
            ->test(AcceptFriendRequests::class)
            ->call('acceptRequest', $sender->id)
            ->assertDispatched('friend-request-accepted')
            ->assertDispatched('friend-added');

        expect(Friend::where([
            'user_id' => $sender->id,
            'friend_id' => $receiver->id,
            'status' => 'accepted',
        ])->exists())->toBeTrue();

        expect(Friend::where([
            'user_id' => $receiver->id,
            'friend_id' => $sender->id,
            'status' => 'accepted',
        ])->exists())->toBeTrue();

        Event::assertDispatched(AmigoAgregado::class);
    });

    it('does not accept if friendship is not pending', function () {
        $receiver = User::factory()->create();
        $sender = User::factory()->create();

        Friend::create([
            'user_id' => $sender->id,
            'friend_id' => $receiver->id,
            'status' => 'accepted',
        ]);

        Livewire::actingAs($receiver)
            ->test(AcceptFriendRequests::class)
            ->call('acceptRequest', $sender->id);
        expect(Friend::where([
            'user_id' => $receiver->id,
            'friend_id' => $sender->id,
        ])->count())->toBe(0);
    });
    it('does nothing if no friend request exists', function () {
        $receiver = User::factory()->create();
        $sender = User::factory()->create();

        Livewire::actingAs($receiver)
            ->test(AcceptFriendRequests::class)
            ->call('acceptRequest', $sender->id)
            ->assertNotDispatched('friend-request-accepted')
            ->assertNotDispatched('friend-added');
    });
});
