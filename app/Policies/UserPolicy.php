<?php
namespace App\Policies;

use App\Model\Capability;
use App\Model\User;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserPolicy
{
    public function isAdmin(): bool
    {
        /** @var User $login */
        $login = app('user');

        if($login->hasCapability(Capability::IS_ADMIN_USER) === false){
            throw new AccessDeniedHttpException();
        }

        return true;
    }

    public function isUser(User $user): bool
    {
        /** @var User $login */
        //$login = app('user');

        return $login->id === $user->id;
    }

    public function updateUser($user): bool
    {
        error_log(__METHOD__);
        return true;
    }

    public function deleteUser($user): bool
    {
        error_log(__METHOD__);
        return true;
    }

    public function addToken($user): bool
    {
        error_log(__METHOD__);
        return true;
    }

    public function removeToken($user): bool
    {
        error_log(__METHOD__);
        return true;
    }
}
