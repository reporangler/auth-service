<?php
namespace App\Service;

use App\Model\Capability;
use App\Model\User;
use App\Model\CapabilityMap;
use RepoRangler\Entity\Repository as RepositoryEntity;
use RepoRangler\Services\MetadataClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class Repository
{
    static public function findById($id)
    {
        $login = Auth::guard('token')->user();
        $metadataClient = app(MetadataClient::class);

        return $metadataClient->getRepositoryById($login->token, $id);
    }

    static public function associateUser(User $user, RepositoryEntity $repository, bool $admin): CapabilityMap
    {
        return CapabilityMap::create([
            'entity_type' => 'user',
            'entity_id' => $user->id,
            'name' => Capability::REPOSITORY_ACCESS,
            'constraint' => [
                'repository' => $repository->name,
                'admin' => $admin,
            ]
        ]);
    }

    static public function whereUser(User $user, RepositoryEntity $repository, ?bool $admin = null)
    {
        $fields = [
            'entity_type' => 'user',
            'entity_id' => $user->id,
            'constraint->repository' => $repository->name,
        ];

        if($admin !== null){
            $fields['constraint->admin'] = $admin;
        }

        return CapabilityMap::whereHas('capability', function (Builder $query){
            $query->where('name', Capability::REPOSITORY_ACCESS);
        })->where($fields);
    }
}
