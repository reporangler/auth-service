<?php
namespace App\Policies;

class AccessTokenPolicy
{
    public function listToken($user): bool
    {
        // Any authenticated user can list tokens
        return true;
    }

    public function addToken($user): bool
    {
        // Any authenticated user can add tokens
        return true;
    }

    public function removeToken($user): bool
    {
        // Any authenticated user can remove their tokens
        return true;
    }
}
