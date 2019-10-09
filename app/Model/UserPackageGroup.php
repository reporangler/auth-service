<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserPackageGroup
{
    static public function findByUserId(int $id): ?Collection
    {
        return UserCapability::whereHas('capability', function(Builder $query){
            $query->whereIn('name', [Capability::PACKAGE_GROUP_ADMIN, Capability::PACKAGE_GROUP_ACCESS]);
        })->where([
            'user_id' => $id,
        ])->get();
    }

    static public function findByPackageGroupId(int $id): ?Collection
    {
        $packageGroup = PackageGroup::findOrFail($id);

        return UserCapability::whereHas('capability', function(Builder $query){
            $query->whereIn('name', [Capability::PACKAGE_GROUP_ADMIN, Capability::PACKAGE_GROUP_ACCESS]);
        })->where([
            'constraint->name' => $packageGroup->name
        ])->get();
    }

    static public function all(): ?Collection
    {
        return UserCapability::all();
    }

    static public function create(PackageGroup $packageGroup, User $user, array $constraint = []): UserCapability
    {
        $userCapability = new UserCapability();
        $userCapability->package_group = $packageGroup;
        $userCapability->constraint = $constraint;
        $userCapability->user()->associate($user);
        $userCapability->save();

        return $userCapability;
    }

    static public function whereUserHasPackageGroup(User $user, PackageGroup $packageGroup, string $access = null)
    {
        $access = $access === null
            ? [Capability::PACKAGE_GROUP_ADMIN, Capability::PACKAGE_GROUP_ACCESS]
            : [$access];

        return UserCapability::whereHas('capability', function (Builder $query) use ($access){
            $query->whereIn('name', $access);
        })->where([
            'user_id' => $user->id,
            'constraint->name' => $packageGroup->name,
        ]);
    }
}
