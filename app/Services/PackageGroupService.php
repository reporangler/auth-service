<?php
namespace App\Services;

use App\Model\User;
use App\Model\Capability;
use App\Model\CapabilityMap;
use RepoRangler\Entity\PackageGroup;
use RepoRangler\Entity\Repository;
use RepoRangler\Services\MetadataClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PackageGroupService
{
    /**
     * @var MetadataClient
     */
    private $metadataClient;

    public function __construct(MetadataClient $metadataClient)
    {
        $this->metadataClient = $metadataClient;
    }

    public function get(): Collection
    {
        return $this->metadataClient->getPackageGroupList();
    }

    public function getById($id): PackageGroup
    {
        return $this->metadataClient->getPackageGroupById($id);
    }

    public function associateUser(User $user, PackageGroup $packageGroup, Repository $repository, bool $admin): CapabilityMap
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

    public function whereUser(User $user, PackageGroup $packageGroup, Repository $repository, ?bool $admin = null)
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
