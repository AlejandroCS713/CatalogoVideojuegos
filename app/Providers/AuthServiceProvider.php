<?php

namespace App\Providers;

use App\Models\Forum\Foro;
use App\Models\users\User;
use App\Policies\ForoPolicy;
use App\Policies\ForumPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * El arreglo de políticas de autorización.
     *
     * @var array
     */
    protected $policies = [
        Foro::class => ForoPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
