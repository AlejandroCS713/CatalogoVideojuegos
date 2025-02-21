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
                ->where('id', '!=', Auth::id()) // Excluir al usuario actual
                ->get();

            $this->message = $this->users->isEmpty() ? 'No se encontraron usuarios' : null;
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
            session()->flash('message', 'Solicitud de amistad enviada');
        } else {
            session()->flash('message', 'Ya has enviado una solicitud a este usuario.');
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
