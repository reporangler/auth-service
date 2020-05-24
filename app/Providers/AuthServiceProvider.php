<?php

namespace App\Providers;

use App\Services\UserAuthenticatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;

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
            /** @var UserAuthenticatorService $userService */
            $userService = app(UserAuthenticatorService::class);

            try{
                $valid = $userService->validateLoginHeaders($request);
            }catch(ValidationException $e){
                abort(400, 'login headers were not valid');
            }

            return $userService->login(
                $valid['reporangler-login-type'],
                $valid['reporangler-login-username'],
                $valid['reporangler-login-password']
            );
        });

        Auth::viaRequest('token', function (Request $request) {
            /** @var UserAuthenticatorService $userService */
            $userService = app(UserAuthenticatorService::class);

            try {
                $token = $userService->validateTokenRequest($request);
            }catch(ValidationException $e) {
                abort(400, 'authorization header was not valid');
            }

            try{
                return $userService->checkToken($token);
            }catch(\Exception $e){
                abort(401, 'Unauthorized');
            }
        });

        Gate::define('is-admin',                'App\Policies\UserPolicy@isAdmin');
        Gate::define('is-user',                 'App\Policies\UserPolicy@isUser');
        Gate::define('user-create',             'App\Policies\UserPolicy@createUser');
        Gate::define('user-update',             'App\Policies\UserPolicy@updateUser');
        Gate::define('user-delete',             'App\Policies\UserPolicy@deleteUser');

        Gate::define('user-list-token',         'App\Policies\AccessTokenPolicy@listToken');
        Gate::define('user-add-token',          'App\Policies\AccessTokenPolicy@addToken');
        Gate::define('user-remove-token',       'App\Policies\AccessTokenPolicy@removeToken');

        Gate::define('is-package-group-admin',  'App\Policies\PackageGroupPolicy@isAdmin');
        Gate::define('package-group-join',      'App\Policies\PackageGroupPolicy@join');
        Gate::define('package-group-leave',     'App\Policies\PackageGroupPolicy@leave');

        Gate::define('repository-join',         'App\Policies\RepositoryPolicy@join');
        Gate::define('repository-leave',        'App\Policies\RepositoryPolicy@leave');
    }
}
