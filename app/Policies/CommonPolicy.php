<?php
namespace App\Policies;

class CommonPolicy
{
    public function isAdmin($user): bool
    {
        error_log(__METHOD__);
        return true;
    }
}
