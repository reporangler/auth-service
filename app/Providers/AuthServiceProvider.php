<?php

namespace App\Providers;

use App\Services\UserAuthenticator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Validator;

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

            $validator = Validator::make($request->headers->all(), [
                'Authorization' => 'required|string'
            ]);

            $data = $validator->validate();

            return $userService->checkToken($data['Authorization']);
        });

        Auth::viaRequest('repo', function (Request $request) {
            /** @var UserAuthenticator $userService */
            $userService = app(UserAuthenticator::class);

            $validator = Validator::make($request->headers->all(), [
                'reporangler-login-type' => 'required|in:http-basic,database,ldap',
                'reporangler-login-username' => 'required|string',
                'reporangler-login-password' => 'required|string',
                'reporangler-login-repository-type' => 'required|string',
            ]);

            $data = $validator->validate();

            return $userService->loginRepoUser(
                $data['reporangler-login-type'],
                $data['reporangler-login-username'],
                $data['reporangler-login-password'],
                $data['reporangler-login-repository-type']
            );
        });
    }
}
