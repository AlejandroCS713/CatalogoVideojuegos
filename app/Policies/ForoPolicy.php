<?php

namespace App\Policies;

use App\Models\Foro\Foro;
use App\Models\users\User;
use Illuminate\Auth\Access\Response;

class ForoPolicy
{

    public function update(User $user, Foro $forum): Response
    {
        return $user->id === $forum->usuario_id
            ? Response::allow()
            : Response::denyWithStatus(404);
    }


    public function delete(User $user, Foro  $forum): Response
    {
        return $user->id === $forum->usuario_id
            ? Response::allow()
            : Response::denyWithStatus(404);
    }
}
