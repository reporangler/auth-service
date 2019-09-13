<?php

namespace App\Providers;

use App\Model\User;
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
        Auth::viaRequest('login', function (Request $request) {
            /** @var UserAuthenticator $userService */
            $userService = app(UserAuthenticator::class);

            $valid = $userService->validateLoginRequest($request);

            return $userService->loginRepoUser(
                $valid['reporangler-login-type'],
                $valid['reporangler-login-username'],
                $valid['reporangler-login-password'],
                $valid['reporangler-login-repository-type']
            );
        });

        Auth::viaRequest('token', function (Request $request) {
            /** @var UserAuthenticator $userService */
            $userService = app(UserAuthenticator::class);

            $token = $userService->validateTokenRequest($request);

            return $userService->checkToken($token);
        });

        Auth::viaRequest('api', function (Request $request) {
            // THIS SHOULD BE CHANGED TO ONLY CHECKING THE TOKEN + API CAPABILITY
            error_log("api request");
            return false;
        });
    }
}
