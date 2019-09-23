<?php
namespace App\Policies;

class PackageGroupPolicy extends GlobalPolicy
{
    public function createPackageGroup($user): bool
    {
        error_log(__METHOD__);
        return true;
    }

    public function updatePackageGroup($user): bool
    {
        error_log(__METHOD__);
        return true;
    }

    public function deletePackageGroup($user): bool
    {
        error_log(__METHOD__);
        return true;
    }
}
