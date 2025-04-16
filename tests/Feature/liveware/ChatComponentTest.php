<?php

use App\Livewire\ChatComponent;
use App\Models\users\Message;
use App\Models\users\User;
use App\Models\users\Friend;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

describe('ChatComponent', function () {
    it('loads messages on mount', function () {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        Friend::create([
            'user_id' => $sender->id,
            'friend_id' => $receiver->id,
            'status' => 'accepted',
        ]);

        Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Hola',
        ]);

        Livewire::actingAs($receiver)
            ->test(ChatComponent::class, ['friendId' => $sender->id])
            ->assertSee('Hola')
            ->assertSee($sender->name);
    });

    it('does not send empty messages', function () {
        $user = User::factory()->create();
        $friend = User::factory()->create();

        Friend::create([
            'user_id' => $user->id,
            'friend_id' => $friend->id,
            'status' => 'accepted',
        ]);

        Livewire::actingAs($user)
            ->test(ChatComponent::class, ['friendId' => $friend->id])
            ->call('sendMessage')
            ->assertNotDispatched('message-sent');
    });

    it('does not send message if users are not friends', function () {
        $user = User::factory()->create();
        $stranger = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ChatComponent::class, ['friendId' => $stranger->id])
            ->set('newMessage', 'No debería enviarse')
            ->call('sendMessage')
            ->assertDontSee('No debería enviarse');
    });

    it('sends a message if user is friend', function () {
        $user = User::factory()->create();
        $friend = User::factory()->create();

        Friend::create([
            'user_id' => $user->id,
            'friend_id' => $friend->id,
            'status' => 'accepted',
        ]);

        Livewire::actingAs($user)
            ->test(ChatComponent::class, ['friendId' => $friend->id])
            ->set('newMessage', 'Test mensaje')
            ->call('sendMessage')
            ->call('loadMessages')
            ->assertSee('Test mensaje');
    });

});
