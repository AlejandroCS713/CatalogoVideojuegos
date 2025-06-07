<?php

namespace App\Http\Controllers\users;

use App\Events\PerfilActualizado;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UpdateAvatarRequest;
use App\Models\users\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load('logros');

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
        $user = Auth::user();
        $avatars = [
            'avatarAngel.png', 'avatarAnimal.png', 'avatarBatman.png',
            'avatarBombero.png', 'avatarCaballera.png', 'avatarCaca.png', 'avatarConejo.png',
            'avatarFlor.png', 'avatarLloron.png', 'avatarMonstruo.png', 'avatarNiñoContento.png',
            'avatarPistola.png', 'avatarPlanta.png', 'avatarPollo.png', 'avatarPro.png',
            'avatarPro2.png'
        ];

        return view('profile.avatar', compact('user', 'avatars'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $cambiosRealizados = [];

        $rules = [
            'name' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ];

        $validatedData = $request->validate($rules);

        if (isset($validatedData['name']) && $validatedData['name'] !== $user->name) {
            $user->name = $validatedData['name'];
            $cambiosRealizados[] = 'name';
        }

        if (isset($validatedData['password']) && !empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
            $cambiosRealizados[] = 'password';
        }

        if ($user->isDirty()) {
            $user->save();
            event(new PerfilActualizado($user, $cambiosRealizados));

            return redirect()->route('profile.avatar')->with('success', __('Profile updated successfully.'));
        }

        return redirect()->route('profile.avatar')->with('info', __('No changes were made to the profile.'));
    }
    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $user = Auth::user();
        $oldAvatar = $user->avatar;

        $user->avatar = $request->avatar;
        $user->save();

        if ($oldAvatar !== $user->avatar) {
            event(new PerfilActualizado($user, ['avatar']));
        }

        return redirect()->route('profile.avatar')->with('success', __('Avatar updated successfully.'));
    }
}
