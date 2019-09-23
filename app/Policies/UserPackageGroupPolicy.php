<?php
namespace App\Policies;

class UserPackageGroupPolicy extends GlobalPolicy
{
    public function createMapping($user): bool
    {
        error_log(__METHOD__);
        return true;
    }

    public function removeMapping($user): bool
    {
        error_log(__METHOD__);
        return true;
    }
}
