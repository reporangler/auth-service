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

    public function createUser($user): bool
    {
        return $user->is_admin_user === true;
    }

    public function updateUser($user, $userId = null): bool
    {
        if ($user->is_admin_user) return true;
        if ($userId && $user->id == $userId) return true;
        return false;
    }

    public function deleteUser($user, $userId = null): bool
    {
        return $user->is_admin_user === true;
    }
}
