<?php
namespace App\Services;

use App\Model\User;

class LDAPAuthenticator
{
    private function getUserBy(array $condition): User
    {
        return User::where($condition)->with([
            'package_groups',
            'access_tokens',
        ])->firstOrFail();
    }

    public function login(string $username, string $password): User
    {
        $user = $this->getUserBy(['username' => $username]);

        abort(401, 'LDAP Login is not defined yet');
    }
}
