<?php

namespace App\Policies;

use App\Models\Foro\MensajeForo;
use App\Models\users\User;
use Illuminate\Auth\Access\Response;

class MensajeForoPolicy
{
    public function delete(User $user, MensajeForo $mensaje)
    {
        return $user->hasRole('moderador') && !$mensaje->usuario->hasRole('admin');
    }
}
