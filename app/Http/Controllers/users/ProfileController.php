<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UpdateAvatarRequest;
use App\Models\users\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $friends = Friend::where(function ($query) {
            $query->where('user_id', Auth::id())
                ->orWhere('friend_id', Auth::id());
        })
            ->where('status', 'accepted')
            ->get();

        return view('profile.profile', compact('user', 'friends'));
    }



    public function editAvatar()
    {
        $avatars = [
            'avatarAngel.png', 'avatarAnimal.png', 'avatarBatman.png',
            'avatarBombero.png', 'avatarCaballera.png', 'avatarCaca.png', 'avatarConejo.png',
            'avatarFlor.png', 'avatarLloron.png', 'avatarMonstruo.png', 'avatarNiñoContento.png',
            'avatarPistola.png', 'avatarPlanta.png', 'avatarPollo.png', 'avatarPro.png',
            'avatarPro2.png'
        ];

        return view('profile.avatar', compact('avatars'));
    }

    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $user = Auth::user();
        $user->avatar = $request->avatar;
        $user->save();

        return redirect()->route('profile')->with('success', 'Avatar actualizado correctamente.');
    }
}
