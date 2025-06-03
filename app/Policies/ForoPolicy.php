<?php

namespace App\Policies;

use App\Models\Foro\Foro;
use App\Models\users\User;
use Illuminate\Auth\Access\Response;

class ForoPolicy
{

    public function create(User $user): Response
    {
        if (! $user->hasVerifiedEmail()) {
            return Response::deny('Debes verificar tu correo electrónico para crear foros.', 403);
        }

        if (! $user->hasRole('user')) {
            return Response::deny('Debes tener el rol de usuario para crear foros.', 403);
        }

        return Response::allow();
    }

    public function update(User $user, Foro $foro): Response
    {
        return $user->id === $foro->usuario_id
            ? Response::allow()
            : Response::deny('No tienes permiso para actualizar este foro.', 403);
    }

    public function delete(User $user, Foro $foro): Response
    {
        return $user->id === $foro->usuario_id
            ? Response::allow()
            : Response::deny('No tienes permiso para eliminar este foro.', 403);
    }
}
