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

    public function getById($id): PackageGroup
    {
        return $this->metadataClient->getPackageGroupById($id);
    }

    public function getByName(string $name): PackageGroup
    {
        return $this->metadataClient->getPackageGroupByName($name);
    }

    public function associateUser(User $user, PackageGroup $packageGroup, Repository $repository, bool $admin, bool $approved): CapabilityMap
    {
        return CapabilityMap::create([
            'entity_type' => CapabilityMap::USER,
            'entity_id' => $user->id,
            'name' => Capability::PACKAGE_GROUP_ACCESS,
            'constraint' => [
                'package_group' => $packageGroup->name,
                'repository' => $repository->name,
                'admin' => $admin,
                'approved' => $approved,
            ]
        ]);
    }

    public function protect(PackageGroup $packageGroup, Repository $repository)
    {
        return CapabilityMap::create([
            'entity_type' => CapabilityMap::PACKAGE_GROUP,
            'entity_id' => Capability::packageGroup()->id,
            'name' => Capability::PACKAGE_GROUP_ACCESS,
            'constraint' => [
                'protected' => true,
                'package_group' => $packageGroup->name,
                'repository' => $repository->name,
            ]
        ]);
    }

    public function unprotect(PackageGroup $packageGroup, Repository $repository)
    {
        $capabilityMap = $this->whereProtected($packageGroup, $repository)->firstOrFail();

        return $capabilityMap->delete() === true;
    }

    public function whereProtected(PackageGroup $packageGroup, Repository $repository)
    {
        $fields = [
            'entity_type' => CapabilityMap::PACKAGE_GROUP,
            'entity_id' => Capability::packageGroup()->id,
            'constraint->package_group' => $packageGroup->name,
            'constraint->repository' => $repository->name,
        ];

        return CapabilityMap::whereHas('capability', function (Builder $query){
            $query->where('name', Capability::PACKAGE_GROUP_ACCESS);
        })->where($fields);
    }

    public function whereUser(User $user, PackageGroup $packageGroup, Repository $repository, ?bool $admin = null)
    {
        $fields = [
            'entity_type' => CapabilityMap::USER,
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
