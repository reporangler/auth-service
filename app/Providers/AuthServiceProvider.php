<?php

namespace App\Providers;

use App\Services\UserAuthenticator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        Auth::viaRequest('api', function (Request $request) {
            $userService = app(UserAuthenticator::class);

            return $userService->loginApiUser($request);
        });

        Auth::viaRequest('token', function (Request $request) {
            $userService = app(UserAuthenticator::class);

            return $userService->checkToken($request);
        });

        Auth::viaRequest('repo', function (Request $request) {
            $userService = app(UserAuthenticator::class);

            return $userService->loginRepoUser($request);
        });
    }
}
