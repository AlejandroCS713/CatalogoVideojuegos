<?php

namespace App\Livewire;

use App\Events\AmigoAgregado;
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

            $reverseFriendship = Friend::where('user_id', Auth::id())
                ->where('friend_id', $id)
                ->where('status', 'pending')
                ->first();

            if ($reverseFriendship) {
                $reverseFriendship->update(['status' => 'accepted']);
            } else {
                Friend::create([
                    'user_id' => Auth::id(),
                    'friend_id' => $id,
                    'status' => 'accepted'
                ]);
            }

            event(new AmigoAgregado(Auth::user(), $friendship->user));
            $this->dispatch('friend-request-accepted');
            $this->dispatch('friend-added');

            session()->flash('message', __('Application accepted'));
        }
    }

    public function render()
    {
        return view('livewire.accept-friend-requests', ['friendRequests' => $this->friendRequests]);
    }
}
