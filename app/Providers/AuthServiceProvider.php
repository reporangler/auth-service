<?php

namespace App\Providers;

use App\Model\User;
use App\Services\UserAuthenticator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use RepoRangler\Entity\AdminUser;
use RepoRangler\Entity\RestUser;

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
        // Login any type of user
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

        Auth::viaRequest('repo', function (Request $request){
            /** @var User $user */
            $user = $request->user('login');

            return false;
        });

        Auth::viaRequest('token', function (Request $request) {
            /** @var UserAuthenticator $userService */
            $userService = app(UserAuthenticator::class);

            $token = $userService->validateTokenRequest($request);

            return $userService->checkToken($token);
        });
    }
}
