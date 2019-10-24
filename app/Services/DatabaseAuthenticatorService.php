<?php
namespace App\Services;

use App\Model\User;

class DatabaseAuthenticatorService
{
    private function getUserBy(array $condition): User
    {
        return User::where($condition)->with(['access_tokens'])->firstOrFail();
    }

    public function login(string $username, string $password): User
    {
        $user = $this->getUserBy(['username' => $username]);

        if(!$user->checkPassword($password)){
            abort(401, 'Unauthorized');
        }

        return $user;
    }
}
