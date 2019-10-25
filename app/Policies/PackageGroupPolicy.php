<?php
namespace App\Policies;

class PackageGroupPolicy extends CommonPolicy
{
    public function canJoin($user): bool
    {
        return_log(__METHOD__);
        return true;
    }

    public function canLeave($user): bool
    {
        return_log(__METHOD__);
        return true;
    }
}
