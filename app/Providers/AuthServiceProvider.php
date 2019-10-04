<?php

namespace App\Providers;

use App\Services\UserAuthenticator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        Auth::viaRequest('login', function (Request $request) {
            /** @var UserAuthenticator $userService */
            $userService = app(UserAuthenticator::class);

            $valid = $userService->validateLoginHeaders($request);

            return $userService->login(
                $valid['reporangler-login-type'],
                $valid['reporangler-login-username'],
                $valid['reporangler-login-password']
            );
        });

        Auth::viaRequest('token', function (Request $request) {
            /** @var UserAuthenticator $userService */
            $userService = app(UserAuthenticator::class);

            $token = $userService->validateTokenRequest($request);

            return $userService->checkToken($token);
        });

        Gate::define('user-create',         'App\Policies\UserPolicy@createUser');
        Gate::define('user-update',         'App\Policies\UserPolicy@updateUser');
        Gate::define('user-delete',         'App\Policies\UserPolicy@deleteUser');

        Gate::define('user-list-token',     'App\Policies\AccessTokenPolicy@listToken');
        Gate::define('user-add-token',      'App\Policies\AccessTokenPolicy@addToken');
        Gate::define('user-remove-token',   'App\Policies\AccessTokenPolicy@removeToken');

        Gate::define('package-group-create', 'App\Policies\PackageGroupPolicy@createPackageGroup');
        Gate::define('package-group-update', 'App\Policies\PackageGroupPolicy@updatePackageGroup');
        Gate::define('package-group-delete', 'App\Policies\PackageGroupPolicy@deletePackageGroup');

        Gate::define('user-package-group-create-mapping', 'App\Policies\UserPackageGroupPolicy@createMapping');
        Gate::define('user-package-group-remove-mapping', 'App\Policies\UserPackageGroupPolicy@removeMapping');
    }
}
