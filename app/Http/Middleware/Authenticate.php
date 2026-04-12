<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

class Authenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        $guard = $guards[0] ?? null;

        $user = auth()->guard($guard)->user();

        if (!$user) {
            throw new AuthenticationException('Unauthenticated.');
        }

        return $next($request);
    }
}
