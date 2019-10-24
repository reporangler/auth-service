<?php
namespace App\Services;

use App\Model\User;
use App\Model\Capability;
use App\Model\CapabilityMap;
use RepoRangler\Entity\PackageGroup as PackageGroupEntity;
use RepoRangler\Entity\Repository as RepositoryEntity;
use RepoRangler\Services\MetadataClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PackageGroup
{
    static public function findById($id)
    {
        $login = Auth::guard('token')->user();
        $metadataClient = app(MetadataClient::class);

        return $metadataClient->getPackageGroupById($login->token, $id);
    }

    static public function associateUser(User $user, PackageGroupEntity $packageGroup, RepositoryEntity $repository, bool $admin): CapabilityMap
    {
        return CapabilityMap::create([
            'entity_type' => 'user',
            'entity_id' => $user->id,
            'name' => Capability::PACKAGE_GROUP_ACCESS,
            'constraint' => [
                'package_group' => $packageGroup->name,
                'repository' => $repository->name,
                'admin' => $admin,
            ]
        ]);
    }

    static public function whereUser(User $user, PackageGroupEntity $packageGroup, RepositoryEntity $repository, ?bool $admin = null)
    {
        $fields = [
            'entity_type' => 'user',
            'entity_id' => $user->id,
            'constraint->package_group' => $packageGroup->name,
            'constraint->repository' => $repository->name,
        ];

        if($admin !== null){
            $fields['constraint->admin'] = $admin;
        }

        return CapabilityMap::whereHas('capability', function (Builder $query){
            $query->where('name', Capability::PACKAGE_GROUP_ACCESS);
        })->where($fields);
    }
}
