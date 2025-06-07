<?php

namespace App\Livewire;

use App\Models\users\Friend;
use App\Models\users\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SearchUsers extends Component
{
    public $searchTerm = '';
    public $users = [];
    public $message = null;

    public function search()
    {
        if (strlen($this->searchTerm) >= 2) {
            $this->users = User::where('name', 'LIKE', "%{$this->searchTerm}%")
                ->where('id', '!=', Auth::id())
                ->get();

            $this->message = $this->users->isEmpty() ? __('No users found') : null;
        } else {
            $this->users = [];
            $this->message = null;
        }
    }

    public function sendFriendRequest($id)
    {
        $existingRequest = Friend::where(function ($query) use ($id) {
            $query->where('user_id', Auth::id())->where('friend_id', $id);
        })
            ->orWhere(function ($query) use ($id) {
                $query->where('user_id', $id)->where('friend_id', Auth::id());
            })
            ->first();

        if (!$existingRequest) {
            Friend::create([
                'user_id' => Auth::id(),
                'friend_id' => $id,
                'status' => 'pending'
            ]);
            $this->dispatch('friend-request-sent');
            session()->flash('message', __('Friend request sent'));
        } else {
            session()->flash('message', __('You have already sent a request to this user'));
        }
    }

    public function render()
    {
        return view('livewire.search-users', [
            'users' => $this->users,
            'message' => $this->message,
        ]);
    }
}
