<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\UserAuthenticatorService;
use Illuminate\Auth\AuthenticationException;

class Authenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        $guard = $guards[0] ?? 'token';

        if ($guard === 'token' || $guard === 'custom-token') {
            $userService = app(UserAuthenticatorService::class);

            try {
                $token = $userService->validateTokenRequest($request);
                $user = $userService->checkToken($token);
                $request->setUserResolver(function () use ($user) {
                    return $user;
                });
                app()->instance('user', $user);
                auth()->setUser($user);
                return $next($request);
            } catch (\Throwable $e) {
                throw new AuthenticationException('Unauthenticated.');
            }
        }

        if ($guard === 'login' || $guard === 'custom-login') {
            $userService = app(UserAuthenticatorService::class);

            try {
                $valid = $userService->validateLoginHeaders($request);
                $user = $userService->login(
                    $valid['reporangler-login-type'],
                    $valid['reporangler-login-username'],
                    $valid['reporangler-login-password']
                );
                $request->setUserResolver(function () use ($user) {
                    return $user;
                });
                return $next($request);
            } catch (\Throwable $e) {
                throw new AuthenticationException($e->getMessage());
            }
        }

        throw new AuthenticationException('Unauthenticated.');
    }
}
