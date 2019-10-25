<?php
namespace App\Model;

class Capability extends \RepoRangler\Entity\Capability
{
    protected $table = 'capability';

    public function scopeIsAdminUser($query)
    {
        return $query->where('name', Capability::IS_ADMIN_USER);
    }

    public function scopePackageGroupAccess($query)
    {
        return $query->where('name', Capability::PACKAGE_GROUP_ACCESS);
    }

    public function scopeRepositoryAccess($query)
    {
        return $query->where('name', Capability::REPOSITORY_ACCESS);
    }
}

