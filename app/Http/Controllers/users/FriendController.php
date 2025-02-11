<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;


use App\Models\users\User;
use App\Models\users\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller {
    public function sendRequest($id)
    {
        $existingRequest = Friend::where(function ($query) use ($id) {
            $query->where('user_id', Auth::id())->where('friend_id', $id);
        })
            ->orWhere(function ($query) use ($id) {
                $query->where('user_id', $id)->where('friend_id', Auth::id());
            })
            ->first();

        if ($existingRequest) {
            return back();
        }

        Friend::create([
            'user_id' => Auth::id(),
            'friend_id' => $id,
            'status' => 'pending'
        ]);

        return redirect()->back();
    }
    public function acceptRequest($id)
    {
        $friendship = Friend::where('user_id', $id)
            ->where('friend_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($friendship) {
            $friendship->update(['status' => 'accepted']);
        }

        return redirect()->back();
    }

    public function removeFriend($id) {
        Friend::where(function($query) use ($id) {
            $query->where('user_id', Auth::id())->where('friend_id', $id);
        })->orWhere(function($query) use ($id) {
            $query->where('user_id', $id)->where('friend_id', Auth::id());
        })->delete();

        return back()->with('success', 'Amigo eliminado.');
    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('query');

        $users = User::where('name', 'LIKE', "%{$query}%")
            ->where('id', '!=', auth()->id())
            ->get();

        return response()->json($users);
    }
}
