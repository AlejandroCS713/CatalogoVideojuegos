<?php

namespace App\Http\Controllers\users;

use App\Events\AmigoAgregado;
use App\Http\Controllers\Controller;


use App\Models\users\User;
use App\Models\users\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FriendController extends Controller {
    public function acceptRequest($id)
    {
        $friendship = Friend::where('user_id', $id)
            ->where('friend_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($friendship) {
            $friendship->update(['status' => 'accepted']);
            event(new AmigoAgregado(Auth::user(), $friendship->user));
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
}

