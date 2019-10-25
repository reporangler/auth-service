<?php

namespace App\Http\Controllers;

use App\Services\PackageGroupService;
use App\Services\RepositoryService;
use App\Model\Capability;
use App\Model\User;
use App\Model\CapabilityMap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Lumen\Routing\Controller as BaseController;
use RepoRangler\Entity\PackageGroup;
use RepoRangler\Entity\Repository;

class CapabilityController extends BaseController
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

    public function joinPackageGroup(Request $request)
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
        $approved = true;

        $capability = $this->packageGroupService->whereUser($user, $packageGroup, $repository)->first();
        if($capability === null){
            $created[] = $this->packageGroupService->associateUser($user, $packageGroup, $repository, $admin, $approved);
        }else{
            $capability->admin = $admin;
            $capability->save();
            $exists[] = $capability->toArray();
        }

        return new JsonResponse(['created' => $created, 'exists' => $exists]);
    }

    public function leavePackageGroup(Request $request)
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

    public function requestJoinPackageGroup(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function joinRepository(Request $request)
    {
        $data = $this->validate($request, [
            'admin' => 'boolean',
        ]);

        $user = $this->validateUser($request);
        $repository = $this->validateRepository($request);

        $created = [];
        $exists = [];

        $admin = array_key_exists('admin', $data) && $data['admin'] === true;
        $approved = true;

        $capability = $this->repositoryService->whereUser($user, $repository)->first();
        if($capability === null){
            $created[] = $this->repositoryService->associateUser($user, $repository, $admin, $approved);
        }else{
            $capability->admin = $admin;
            $capability->save();
            $exists[] = $capability->toArray();
        }

        return new JsonResponse(['created' => $created, 'exists' => $exists]);
    }

    public function leaveRepository(Request $request)
    {
        $user = $this->validateUser($request);
        $repository = $this->validateRepository($request);

        $deleted = [];

        $capability = $this->repositoryService->whereUser($user, $repository)->get();
        if($capability instanceOf CapabilityMap) {
            $deleted[] = $capability->toArray();
            $capability->delete();
        }

        return new JsonResponse(['deleted' => $deleted]);
    }

    public function requestJoinRepository(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function protectPackageGroup(Request $request)
    {
        Gate::allows('is-admin');

        $packageGroup = $this->validatePackageGroup($request);
        $repository = $this->validateRepository($request);

        return new JsonResponse([
            'protected' => $this->packageGroupService->protect($packageGroup, $repository)
        ]);
    }

    public function unprotectPackageGroup(Request $request)
    {
        Gate::allows('is-admin');

        $packageGroup = $this->validatePackageGroup($request);
        $repository = $this->validateRepository($request);

        return new JsonResponse([
            'protected' => !$this->packageGroupService->unprotect($packageGroup, $repository)
        ]);
    }

    public function protectRepository(Request $request)
    {
        Gate::allows('is-admin');

        $repository = $this->validateRepository($request);

        return new JsonResponse([
            'protected' => $this->repositoryService->protect($repository)
        ]);
    }

    public function unprotectRepository(Request $request)
    {
        Gate::allows('is-admin');

        $repository = $this->validateRepository($request);

        return new JsonResponse([
            'protected' => !$this->repositoryService->unprotect($repository)
        ]);
    }
}
