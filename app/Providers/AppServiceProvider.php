<?php

namespace App\Providers;

use App\Models\Foro\Foro;
use App\Models\users\User;
use App\Policies\ForoPolicy;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        if (app()->environment('local')) {
            Config::set('queue.default', 'sync');
        } else {
            Config::set('queue.default', 'database');
        }

        Gate::define('verLogros', function (User $user) {
            return $user->logros()->exists();
        });

        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });
    }
}
