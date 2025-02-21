<?php

namespace App\Policies;

use App\Models\Forum\Foro;
use App\Models\users\User;
use Illuminate\Auth\Access\Response;

class ForoPolicy
{
    public function update(User $user, Foro $foro): bool
    {
        return $user->hasRole('admin') || $user->id === $foro->user_id;
    }

    public function delete(User $user, Foro $foro): bool
    {
        return $user->hasRole('admin') || $user->id === $foro->user_id;
    }
}
