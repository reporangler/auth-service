<?php
namespace App\Services;

use App\Model\User;

class LDAPAuthenticator
{
    public function login(User $user, $password)
    {
        throw new UnauthorizedHttpException("Basic", "Unauthorized");
    }
}
