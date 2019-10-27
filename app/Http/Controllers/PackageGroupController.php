<?php

namespace App\Http\Controllers;

use App\Model\User;
use App\Model\CapabilityMap;
use App\Services\PackageGroupService;
use App\Services\RepositoryService;
use RepoRangler\Entity\PackageGroup;
use RepoRangler\Entity\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Lumen\Routing\Controller as BaseController;

class PackageGroupController extends BaseController
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

    private function validateUser(Request $request): User
    {
        $data = $this->validate($request, [
            'user_id' => 'required|integer|min:1',
        ]);

        return User::findOrFail($data['user_id']);
    }

    private function validatePackageGroup(Request $request): PackageGroup
    {
        $data = $this->validate($request, [
            'package_group_id' => 'required_xor:package_group|integer|min:1',
            'package_group' => 'required_xor:package_group_id|string|min:1',
        ]);

        return array_key_exists('package_group_id', $data)
            ? $this->packageGroupService->getById($data['package_group_id'])
            : $this->packageGroupService->getByName($data['package_group']);
    }

    private function validateRepository(Request $request): Repository
    {
        $data = $this->validate($request, [
            'repository_id' => 'required_xor:repository|integer|min:1',
            'repository' => 'required_xor:repository_id|string|min:1',
        ]);

        return array_key_exists('repository_id', $data)
            ? $this->repositoryService->getById($data['repository_id'])
            : $this->repositoryService->getByName($data['repository']);
    }

    public function join(Request $request)
    {
        $data = $this->validate($request, [
            'admin' => 'boolean',
        ]);

        $user = $this->validateUser($request);
        $packageGroup = $this->validatePackageGroup($request);
        $repository = $this->validateRepository($request);

        $created = [];
        $exists = [];

        $admin = array_key_exists('admin', $data) && $data['admin'] === true;

        // Only let admin users create other admin users
        if($admin) Gate::allows('is-admin');

        //  Are you already in the group?
        $capability = $this->packageGroupService->whereUser($user, $packageGroup, $repository)->first();
        if($capability === null){
            // Nope, you aren't, but first are you allowed to join the group?
            if(Gate::allows('package-group-join', [$user, $packageGroup, $repository])){
                // You can join the group
                $created[] = $this->packageGroupService->join($user, $packageGroup, $repository, $admin);
            }else{
                // No, you have to request to join
                $created[] = $this->packageGroupService->requestJoin($user, $packageGroup, $repository, $admin);
            }
        }else{
            // Yes, you are
            $capability->admin = $admin;
            $capability->save();
            $exists[] = $capability->toArray();
        }

        return new JsonResponse(['created' => $created, 'exists' => $exists]);
    }

    public function leave(Request $request)
    {
        $user = $this->validateUser($request);
        $packageGroup = $this->validatePackageGroup($request);
        $repository = $this->validateRepository($request);

        $deleted = [];

        $capability = $this->packageGroupService->whereUser($user, $packageGroup, $repository)->get();
        if($capability instanceOf CapabilityMap) {
            $deleted[] = $capability->toArray();
            $capability->delete();
        }

        return new JsonResponse(['deleted' => $deleted]);
    }

    public function getApprovals(Request $request)
    {
        Gate::allows('is-package-group-admin');

        $loginUser = $request->user();

        $list = CapabilityMap::user($loginUser)->packageGroup()->admin()->get();

        $approvals = [];

        foreach($list as $admin){
            $found = CapabilityMap::user()->packageGroup()->approvals(
                $admin->constraint['package_group'],
                $admin->constraint['repository']
            )->get();

            $approvals = collect($found)->merge($approvals);
        }

        return new JsonResponse(['approvals' => $approvals]);
    }

    public function approveRequest(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'not implemented']);
    }

    public function rejectRequest(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'not implemented']);
    }

    public function protect(Request $request)
    {
        Gate::allows('is-admin');

        $packageGroup = $this->validatePackageGroup($request);
        $repository = $this->validateRepository($request);

        $protected = false;

        try{
            $protected = $this->packageGroupService->protect($packageGroup, $repository);
        }catch(\PDOException $e){
            if(intval($e->getCode()) === 23505) {
                $protected = true;
            }else{
                throw $e;
            }
        }

        return new JsonResponse(['protected' => $protected]);
    }

    public function unprotect(Request $request)
    {
        Gate::allows('is-admin');

        $packageGroup = $this->validatePackageGroup($request);
        $repository = $this->validateRepository($request);

        $protected = true;

        try{
            $protected = !$this->packageGroupService->unprotect($packageGroup, $repository);
        }catch(\PDOException $e){
            if(intval($e->getCode()) === 23505) {
                $protected = false;
            }else{
                throw $e;
            }
        }

        return new JsonResponse(['protected' => $protected]);
    }
}
