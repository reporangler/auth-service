<?php
namespace App\Policies;

use App\Model\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserPolicy
{
    public function isAdmin(User $user): bool
    {
        if($user->is_admin_user === false){
            throw new AccessDeniedHttpException('Only an administrator can perform this action');
        }

        return true;
    }

    public function isUser(User $user): bool
    {
        /** @var User $login */
        $login = app('user');

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
