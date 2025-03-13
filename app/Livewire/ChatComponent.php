<?php

namespace App\Livewire;

use App\Models\users\Friend;
use App\Models\users\Message;
use App\Models\users\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatComponent extends Component
{
    public $friend;
    public $messages = [];
    public $newMessage = '';

    public function mount($friendId)
    {
        $this->friend = User::findOrFail($friendId);
        $this->loadMessages();
    }

    #[On('message-sent')]
    #[On('message-received')]
    public function loadMessages()
    {
        $this->messages = Message::where(function ($query) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $this->friend->id);
        })
            ->orWhere(function ($query) {
                $query->where('sender_id', $this->friend->id)
                    ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function sendMessage()
    {
        if (!$this->newMessage) return;

        $isFriend = Friend::where(function ($query) {
            $query->where('user_id', Auth::id())
                ->where('friend_id', $this->friend->id);
        })
            ->orWhere(function ($query) {
                $query->where('user_id', $this->friend->id)
                    ->where('friend_id', Auth::id());
            })
            ->where('status', 'accepted')->exists();

        if (!$isFriend) {
            session()->flash('error', 'Solo puedes enviar mensajes a tus amigos.');
            return;
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->friend->id,
            'message' => $this->newMessage
        ]);

        $receiver = User::find($this->friend->id);

        if ($receiver) {
            $receiver->notify(new NewMessageNotification($message));
        }

        $this->newMessage = '';
        $this->dispatch('message-sent');
        session()->flash('success', 'Mensaje enviado correctamente.');
    }

    public function render()
    {
        return view('livewire.chat-component', [
            'messages' => $this->messages,
            'friend' => $this->friend
        ])->extends('layouts.app')
        ->section('content');
    }
}
