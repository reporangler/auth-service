<?php
namespace App\Policies;

class GlobalPolicy
{
    public function isAdmin($user): bool
    {
        error_log(__METHOD__);
        return true;
    }
}
