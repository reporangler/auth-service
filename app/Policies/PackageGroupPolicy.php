<?php
namespace App\Policies;

use App\Model\CapabilityMap;
use App\Model\User;
use App\Services\PackageGroupService;
use App\Services\RepositoryService;
use RepoRangler\Entity\PackageGroup;
use RepoRangler\Entity\Repository;

class PackageGroupPolicy
{
    /**
     * @var PackageGroupService
     */
    private $packageGroupService;

    /**
     * @var RepositoryService
     */
    private $repositoryService;

    public function __construct(PackageGroupService $packageGroupService, RepositoryService $repositoryService)
    {
        $this->packageGroupService = $packageGroupService;
        $this->repositoryService = $repositoryService;
    }

    public function isAdmin(User $loginUser)
    {
        if($loginUser->is_admin_user) return true;

        $list = CapabilityMap::user($loginUser)->packageGroup()->admin()->get();

        if(!empty($list)) return true;

        throw new AccessDeniedHttpException('Only an administrator or package administrator can perform this action');
    }

    public function join(User $loginUser, User $requestUser, PackageGroup $packageGroup, Repository $repository): bool
    {
        if($loginUser->is_admin_user){
            return true;
        }

        if($this->packageGroupService->isProtected($packageGroup, $repository)){
            return false;
        }

        return true;
    }

    public function leave($user): bool
    {
        return_log(__METHOD__);
        return true;
    }
}
