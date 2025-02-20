<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SendMessageRequest;
use App\Models\users\Friend;
use App\Models\users\Message;
use App\Models\users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewMessageNotification;


class MessageController extends Controller {
    public function sendMessage(SendMessageRequest $request)
    {
        $isFriend = Friend::where(function ($query) use ($request) {
            $query->where('user_id', Auth::id())
                ->where('friend_id', $request->receiver_id);
        })->orWhere(function ($query) use ($request) {
            $query->where('user_id', $request->receiver_id)
                ->where('friend_id', Auth::id());
        })->where('status', 'accepted')->exists();

        if (!$isFriend) {
            return back()->with('error', 'Solo puedes enviar mensajes a tus amigos.');
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);

        $receiver = User::find($request->receiver_id);
        $receiver->notify(new NewMessageNotification($message));

        return redirect()->back()->with('success', 'Mensaje enviado correctamente.');
    }

    public function chat($friend_id) {
        $friend = User::findOrFail($friend_id);

        $messages = Message::where(function ($query) use ($friend_id) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $friend_id);
        })->orWhere(function ($query) use ($friend_id) {
            $query->where('sender_id', $friend_id)
                ->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();

        return view('messages.chat', compact('friend', 'messages'));
    }
}
