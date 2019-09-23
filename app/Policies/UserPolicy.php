<?php
namespace App\Policies;

class UserPolicy extends GlobalPolicy
{
    public function createUser($user): bool
    {
        error_log(__METHOD__);
        return true;
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
