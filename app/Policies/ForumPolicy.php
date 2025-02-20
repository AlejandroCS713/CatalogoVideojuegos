<?php

namespace App\Policies;
use Illuminate\Auth\Access\Response;
use App\Models\users\User;

class ForumPolicy
{
    public function writeMessage(User $user)
    {
        return $user->forums()
            ->exists()? Response::allow()
            : Response::deny('You do not own this post.');
    }
}
