<?php

namespace App\Policies;

use App\Models\Foro\RespuestaForo;
use App\Models\users\User;
use Illuminate\Auth\Access\Response;

class RespuestaForoPolicy
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

    public function update(User $user, RespuestaForo $respuestaForo): Response
    {
        if ($user->id === $respuestaForo->usuario_id) {
            return Response::allow();
        }

        if ($user->hasRole('moderador') && ! $respuestaForo->usuario->hasRole('admin')) {
            return Response::allow();
        }

        return Response::deny('No tienes permiso para actualizar esta respuesta.', 403);
    }

    public function delete(User $user, RespuestaForo $respuestaForo): Response
    {
        if ($user->id === $respuestaForo->usuario_id) {
            return Response::allow();
        }

        if ($user->hasRole('moderador') && ! $respuestaForo->usuario->hasRole('admin')) {
            return Response::allow();
        }

        return Response::deny('No tienes permiso para eliminar esta respuesta.', 403);
    }
}
