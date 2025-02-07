<?php

namespace App\Http\Controllers\profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user(); // Obtiene el usuario autenticado
        return view('/profile/profile', compact('user')); // Envía los datos a la vista
    }

    public function settings()
    {
        return view('profile.settings');
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

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|string'
        ]);

        $user = Auth::user();
        $user->avatar = $request->avatar;
        $user->save();

        return redirect()->route('profile')->with('success', 'Avatar actualizado correctamente.');
    }
}
