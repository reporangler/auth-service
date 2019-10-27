<?php
namespace App\Policies;

class RepositoryPolicy
{
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
