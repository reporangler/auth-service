<?php
namespace App\Meta;

use App\Model\Capability;
use App\Model\User;
use App\Model\UserCapability;
use RepoRangler\Entity\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserToRepository
{
    static public function create(string $cap, User $user, Repository $repository): UserCapability
    {
        $capability = new UserCapability([
            'name' => $cap,
            'user_id' => $user->id,
            'constraint' => [
                'repository' => $repository->name
            ]
        ]);
        $capability->save();

        return $capability;
    }

    static public function where(User $user, Repository $repository, string $access = null)
    {
        $access = $access === null
            ? [Capability::REPOSITORY_ACCESS, Capability::REPOSITORY_ADMIN]
            : [$access];

        return UserCapability::whereHas('capability', function (Builder $query) use ($access){
            $query->whereIn('name', $access);
        })->where([
            'user_id' => $user->id,
            'constraint->repository' => $repository->name,
        ]);
    }
}
