<?php
namespace App\Services;

use App\Model\User;

class LDAPAuthenticator
{
    public function login(User $user, $password):User
    {
        throw new UnauthorizedHttpException("Basic", "Unauthorized");
    }
}
