<?php

namespace App\Livewire;

use App\Models\users\Friend;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class AcceptFriendRequests extends Component
{
    public $friendRequests = [];

    public function mount()
    {
        $this->friendRequests = Auth::user()->friendRequests()->with('user')->get();
    }

    #[On('friend-request-sent')]
    #[On('friend-request-accepted')]
    public function loadRequests()
    {
            $this->friendRequests = Auth::user()->friendRequests()->with('user')->get();
    }

    public function acceptRequest($id)
    {
        $friendship = Friend::where('user_id', $id)
            ->where('friend_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($friendship) {
            $friendship->update(['status' => 'accepted']);
            session()->flash('message', 'Solicitud aceptada');

            $this->dispatch('friend-request-accepted');
        }
    }

    public function render()
    {
        return view('livewire.accept-friend-requests', ['friendRequests' => $this->friendRequests]);
    }
}
