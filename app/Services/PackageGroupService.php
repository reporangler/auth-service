<?php
namespace App\Services;

use App\Model\User;
use App\Model\Capability;
use App\Model\CapabilityMap;
use RepoRangler\Entity\PackageGroup;
use RepoRangler\Entity\Repository;
use RepoRangler\Services\MetadataClient;
use Illuminate\Database\Eloquent\Builder;
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

    private function associateUser(User $user, PackageGroup $packageGroup, Repository $repository, bool $admin, bool $approved): CapabilityMap
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

    public function requestJoin(User $user, PackageGroup $packageGroup, Repository $repository, bool $admin): CapabilityMap
    {
        return $this->associateUser($user, $packageGroup, $repository, $admin, false);
    }

    public function join(User $user, PackageGroup $packageGroup, Repository $repository, bool $admin): CapabilityMap
    {
        return $this->associateUser($user, $packageGroup, $repository, $admin, true);
    }

    public function isProtected(PackageGroup $packageGroup, Repository $repository): bool
    {
        return CapabilityMap::where([
            'entity_type' => CapabilityMap::PACKAGE_GROUP,
            'entity_id' => Capability::packageGroupAccess()->firstOrFail()->id,
            'capability_id' => Capability::packageGroupAccess()->firstOrFail()->id,
            'constraint->protected' => true,
            'constraint->package_group' => $packageGroup->name,
            'constraint->repository' => $repository->name,
        ])->first() !== null;
    }

    public function protect(PackageGroup $packageGroup, Repository $repository): CapabilityMap
    {
        return CapabilityMap::create([
            'entity_type' => CapabilityMap::PACKAGE_GROUP,
            'entity_id' => Capability::packageGroupAccess()->firstOrFail()->id,
            'name' => Capability::PACKAGE_GROUP_ACCESS,
            'constraint' => [
                'protected' => true,
                'package_group' => $packageGroup->name,
                'repository' => $repository->name,
            ]
        ]);
    }

    public function unprotect(PackageGroup $packageGroup, Repository $repository): bool
    {
        $capabilityMap = $this->whereProtected($packageGroup, $repository)->firstOrFail();

        return $capabilityMap->delete() === true;
    }

    public function whereProtected(PackageGroup $packageGroup, Repository $repository)
    {
        $fields = [
            'entity_type' => CapabilityMap::PACKAGE_GROUP,
            'entity_id' => Capability::packageGroupAccess()->firstOrFail()->id,
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
