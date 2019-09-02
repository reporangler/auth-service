<?php
namespace App\Services;

use App\Model\User;

class LDAPAuthenticator
{
    public function login(User $user, $password)
    {
        if(!$user->checkPassword($password)){
            throw new UnauthorizedHttpException("Basic", "Unauthorized");
        }

        return $user;
    }
}
