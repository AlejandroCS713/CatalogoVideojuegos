<?php

namespace App\Livewire;

use App\Models\users\Friend;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class FriendList extends Component
{
    public $friends = [];

    public function mount()
    {
        $this->loadFriends();
    }

    #[On('friend-request-accepted')]
    #[On('friend-added')]
    #[On('friend-removed')]
    public function loadFriends()
    {
        $this->friends = Auth::user()->friends()->get();
    }
    public function removeFriend($id)
    {
        $friendship = Friend::where(function ($query) use ($id) {
            $query->where('user_id', Auth::id())
                ->where('friend_id', $id);
        })
            ->orWhere(function ($query) use ($id) {
                $query->where('user_id', $id)
                    ->where('friend_id', Auth::id());
            })
            ->first();

        if ($friendship) {
            $friendship->delete();

            $reverseFriendship = Friend::where(function ($query) use ($id) {
                $query->where('user_id', $id)
                    ->where('friend_id', Auth::id());
            })
                ->orWhere(function ($query) use ($id) {
                    $query->where('user_id', Auth::id())
                        ->where('friend_id', $id);
                })
                ->first();

            if ($reverseFriendship) {
                $reverseFriendship->delete();
            }

            session()->flash('message', __('Friend successfully removed'));
            $this->dispatch('friend-removed');
            return redirect()->route('profile');
        }
    }

}
