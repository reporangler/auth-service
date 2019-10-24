<?php
namespace App\Services;

use App\Model\Capability;
use App\Model\User;
use App\Model\CapabilityMap;
use RepoRangler\Entity\Repository;
use RepoRangler\Services\MetadataClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class RepositoryService
{
    /**
     * @var MetadataClient
     */
    private $metadataClient;

    public function __construct(MetadataClient $client)
    {
        $this->metadataClient = $client;
    }

    public function getById($id): Repository
    {
        return $this->metadataClient->getRepositoryById($id);
    }

    public function getByName(string $name): Repository
    {
        return $this->metadataClient->getRepositoryByName($name);
    }

    public function associateUser(User $user, Repository $repository, bool $admin): CapabilityMap
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

    public function whereUser(User $user, Repository $repository, ?bool $admin = null)
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
