<?php

namespace App\Policies;

use App\Models\Foro\MensajeForo;
use App\Models\users\User;
use Illuminate\Auth\Access\Response;

class MensajeForoPolicy
{

    public function create(User $user): Response
    {
        if (! $user->hasVerifiedEmail()) {
            return Response::deny('Debes verificar tu correo electrónico para crear mensajes.', 403);
        }

        if (! $user->hasRole('user')) {
            return Response::deny('Debes tener el rol de usuario para crear mensajes.', 403);
        }

        return Response::allow();
    }

    public function update(User $user, MensajeForo $mensajeForo): Response
    {
        if ($user->id === $mensajeForo->usuario_id) {
            return Response::allow();
        }

        if ($user->hasRole('moderador') && ! $mensajeForo->usuario->hasRole('admin')) {
            return Response::allow();
        }

        return Response::deny('No tienes permiso para actualizar este mensaje.', 403);
    }

    public function delete(User $user, MensajeForo $mensajeForo): Response
    {
        if ($user->id === $mensajeForo->usuario_id) {
            return Response::allow();
        }

        if ($user->hasRole('moderador') && ! $mensajeForo->usuario->hasRole('admin')) {
            return Response::allow();
        }

        return Response::deny('No tienes permiso para eliminar este mensaje.', 403);
    }
}
