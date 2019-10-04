<?php
namespace App\Policies;

class AccessTokenPolicy extends GlobalPolicy
{
    public function listToken($user): bool
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
