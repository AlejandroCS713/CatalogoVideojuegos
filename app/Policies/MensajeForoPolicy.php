<?php

namespace App\Policies;

use App\Models\Foro\MensajeForo;
use App\Models\users\User;
use Illuminate\Auth\Access\Response;

class MensajeForoPolicy
{
    public function delete(User $user, MensajeForo $mensaje)
    {
        if ($user->id === $mensaje->usuario_id) {
            return true;
        }
        return $user->hasRole('moderador') && !$mensaje->usuario->hasRole('admin');
    }
}
