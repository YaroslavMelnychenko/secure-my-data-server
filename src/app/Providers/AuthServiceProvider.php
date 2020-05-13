<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Carbon\Carbon;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $sessionLifetime = (int) config('passport.session_lifetime');

        $accessTokenLifetime = $sessionLifetime;
        $refreshTokenLifetime = 2 * $accessTokenLifetime;

        $this->registerPolicies();
        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addMinutes($accessTokenLifetime));
        Passport::refreshTokensExpireIn(Carbon::now()->addMinutes($refreshTokenLifetime));
    }
}
