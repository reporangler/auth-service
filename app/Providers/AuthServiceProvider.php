<?php

namespace App\Providers;

use App\Services\UserAuthenticatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        Auth::viaRequest('custom-login', function (Request $request) {
            $userService = app(UserAuthenticatorService::class);

            $valid = $userService->validateLoginHeaders($request);

            return $userService->login(
                $valid['reporangler-login-type'],
                $valid['reporangler-login-username'],
                $valid['reporangler-login-password']
            );
        });

        Auth::viaRequest('custom-token', function (Request $request) {
            try {
                $userService = app(UserAuthenticatorService::class);
                $token = $userService->validateTokenRequest($request);
                file_put_contents('/tmp/auth-debug.log', "Token extracted: $token\n", FILE_APPEND);
                $user = $userService->checkToken($token);
                file_put_contents('/tmp/auth-debug.log', "User found: {$user->username}\n", FILE_APPEND);
                return $user;
            } catch (\Throwable $e) {
                file_put_contents('/tmp/auth-debug.log', "Auth error: " . get_class($e) . ": " . $e->getMessage() . "\n", FILE_APPEND);
                return null;
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
    }
}
