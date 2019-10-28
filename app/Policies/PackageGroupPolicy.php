<?php
namespace App\Policies;

use App\Model\User;
use App\Model\CapabilityMap;
use App\Services\PackageGroupService;
use RepoRangler\Entity\PackageGroup;
use RepoRangler\Entity\Repository;

class PackageGroupPolicy
{
    /**
     * @var PackageGroupService
     */
    private $packageGroupService;

    public function __construct(PackageGroupService $packageGroupService)
    {
        $this->packageGroupService = $packageGroupService;
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
