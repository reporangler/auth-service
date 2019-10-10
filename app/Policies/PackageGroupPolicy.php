<?php
namespace App\Policies;

class PackageGroupPolicy extends GlobalPolicy
{
    public function join($user): bool
    {
        return_log(__METHOD__);
        return true;
    }

    public function leave($user): bool
    {
        return_log(__METHOD__);
        return true;
    }

    public function protect($user): bool
    {
        return_log(__METHOD__);
        return true;
    }

    public function unprotect($user): bool
    {
        return_log(__METHOD__);
        return true;
    }
}
