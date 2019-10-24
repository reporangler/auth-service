<?php

namespace App\Http\Controllers;

use App\Services\PackageGroupService;
use App\Services\RepositoryService;
use App\Model\Capability;
use App\Model\User;
use App\Model\CapabilityMap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class CapabilityController extends BaseController
{
    public function joinPackageGroup(Request $request, PackageGroupService $packageGroupService, RepositoryService $repositoryService)
    {
        $data = $this->validate($request, [
            'user_id' => 'required|integer|min:1',
            'package_group_id' => 'required_xor:package_group|integer|min:1',
            'package_group' => 'required_xor:package_group_id|string|min:1',
            'repository_id' => 'required_xor:repository|integer|min:1',
            'repository' => 'required_xor:repository_id|string|min:1',
            'admin' => 'boolean',
        ]);

        $user = User::findOrFail($data['user_id']);

        $packageGroup = array_key_exists('package_group_id', $data)
            ? $packageGroupService->getById($data['package_group_id'])
            : $packageGroupService->getByName($data['package_group']);

        $repository = array_key_exists('repository_id', $data)
            ? $repositoryService->getById($data['repository_id'])
            : $repositoryService->getByName($data['repository']);

        $created = [];
        $exists = [];

        $admin = array_key_exists('admin', $data) && $data['admin'] === true;

        $capability = $packageGroupService->whereUser($user, $packageGroup, $repository)->first();
        if($capability === null){
            $created[] = $packageGroupService->associateUser($user, $packageGroup, $repository, $admin);
        }else{
            $capability->admin = $admin;
            $capability->save();
            $exists[] = $capability->toArray();
        }

        return new JsonResponse(['created' => $created, 'exists' => $exists]);
    }

    public function leavePackageGroup(Request $request, PackageGroupService $packageGroupService, RepositoryService $repositoryService)
    {
        $data = $this->validate($request, [
            'user_id' => 'required|integer|min:1',
            'package_group_id' => 'required_xor:package_group|integer|min:1',
            'package_group' => 'required_xor:package_group_id|string|min:1',
            'repository_id' => 'required_xor:repository|integer|min:1',
            'repository' => 'required_xor:repository_id|string|min:1',
        ]);

        $user = User::findOrFail($data['user_id']);

        $packageGroup = array_key_exists('package_group_id', $data)
            ? $packageGroupService->getById($data['package_group_id'])
            : $packageGroupService->getByName($data['package_group']);

        $repository = array_key_exists('repository_id', $data)
            ? $repositoryService->getById($data['repository_id'])
            : $repositoryService->getByName($data['repository']);

        $deleted = [];

        $capability = $packageGroupService->whereUser($user, $packageGroup, $repository)->get();
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

    public function joinRepository(Request $request, RepositoryService $repositoryService)
    {
        $data = $this->validate($request, [
            'user_id' => 'required|integer|min:1',
            'repository_id' => 'required_xor:repository|integer|min:1',
            'repository' => 'required_xor:repository_id|string|min:1',
            'admin' => 'boolean',
        ]);

        $user = User::findOrFail($data['user_id']);

        $repository = array_key_exists('repository_id', $data)
            ? $repositoryService->getById($data['repository_id'])
            : $repositoryService->getByName($data['repository']);

        $created = [];
        $exists = [];

        $admin = array_key_exists('admin', $data) && $data['admin'] === true;

        $capability = $repositoryService->whereUser($user, $repository)->first();
        if($capability === null){
            $created[] = $repositoryService->associateUser($user, $repository, $admin);
        }else{
            $capability->admin = $admin;
            $capability->save();
            $exists[] = $capability->toArray();
        }

        return new JsonResponse(['created' => $created, 'exists' => $exists]);
    }

    public function leaveRepository(Request $request, RepositoryService $repositoryService)
    {
        $data = $this->validate($request, [
            'user_id' => 'required|integer|min:1',
            'repository_id' => 'required_xor:repository|integer|min:1',
            'repository' => 'required_xor:repository_id|string|min:1',
        ]);

        $user = User::findOrFail($data['user_id']);

        $repository = array_key_exists('repository_id', $data)
            ? $repositoryService->getById($data['repository_id'])
            : $repositoryService->getByName($data['repository']);

        $deleted = [];

        $capability = $repositoryService->whereUser($user, $repository)->get();
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
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function unprotectPackageGroup(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function protectRepository(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function unprotectRepository(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }
}
