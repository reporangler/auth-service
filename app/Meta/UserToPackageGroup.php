<?php
namespace App\Meta;

use App\Model\Capability;
use App\Model\User;
use App\Model\UserCapability;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserToPackageGroup
{
    static public function create(string $cap, User $user, PackageGroup $packageGroup): UserCapability
    {
        $capability = new UserCapability([
            'name' => $cap,
            'user_id' => $user->id,
            'constraint' => [
                'name' => $packageGroup->name
            ]
        ]);
        $capability->save();

        return $capability;
    }

    static public function where(User $user, PackageGroup $packageGroup, string $access = null)
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
