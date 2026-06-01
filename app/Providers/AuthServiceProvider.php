<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Super-admin: any admin-panel user holding the "Admin" role bypasses every
        // permission check — granting full access to all sidebar menus and routes,
        // including routes whose permission records may not exist yet. Spatie's
        // permission middleware uses can(), so Gate::before applies to it as well.
        Gate::before(function ($user, $ability) {
            if ($user instanceof User && $user->hasRole('Admin')) {
                return true;
            }
            return null; // fall through to normal checks for everyone else
        });
    }
}
