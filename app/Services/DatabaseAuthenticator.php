<?php
namespace App\Services;

use App\Model\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DatabaseAuthenticator
{
    public function login(User $user, $password): User
    {
        if(!$user->checkPassword($password)){
            throw new UnauthorizedHttpException("Basic", "Unauthorized");
        }

        return $user;
    }
}
