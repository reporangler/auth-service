<?php

namespace App\Http\Controllers;

use App\Model\PackageGroup;
use App\Model\User;
use App\Model\UserPackageGroup;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UserPackageGroupController extends BaseController
{
    public function findByUserId(int $userId): JsonResponse
    {
        $list = UserPackageGroup::findByUserId($userId);

        return new JsonResponse([
            'count' => count($list),
            'data' => $list,
        ]);
    }

    public function findByPackageGroupId(int $groupId): JsonResponse
    {
        $list = UserPackageGroup::findByPackageGroupId($groupId);

        return new JsonResponse([
            'count' => count($list),
            'data' => $list,
        ]);
    }

    public function getList(): JsonResponse
    {
        $list = UserPackageGroup::all();

        return new JsonResponse([
            'count' => count($list),
            'data' => $list
        ], count($list) ? 200 : 404);
    }

    public function createMapping(Request $request): JsonResponse
    {
        $request->user()->can('user-package-group-create-mapping');

        $authSchema = [
            'user_id' => 'required|int|min:1',
            'package_group_id' => 'required|int|min:1',
        ];

        $data = $this->validate($request,$authSchema);

        $user = User::findOrFail($data['user_id']);
        $packageGroup = PackageGroup::findOrFail($data['package_group_id']);

        $userPackageGroup = UserPackageGroup::whereUserHasPackageGroup($user, $packageGroup);

        if($userPackageGroup){
            throw new UnprocessableEntityHttpException("User Package Group with '{$user->username} (id: {$user->id})' and '{$packageGroup->name} (id: {$packageGroup->id})' already exists");
        }

        $userPackageGroup = UserPackageGroup::create($packageGroup, $user);

        return new JsonResponse($userPackageGroup, 200);
    }

    public function deleteMapping(Request $request, int $userId, int $groupId): JsonResponse
    {
        $request->user()->can('user-package-group-delete-mapping');

        $user = User::findOrFail($userId);
        $packageGroup = PackageGroup::findOrFail($groupId);

        $userPackageGroup = UserPackageGroup::whereUserHasPackageGroup($user, $packageGroup);

        $deleted = [];

        if($userPackageGroup){
            $deleted[] = $userPackageGroup->toArray();
            $userPackageGroup->delete();
        }

        return new JsonResponse([
            'count' => count($deleted),
            'deleted' => $deleted,
        ], count($deleted) ? 200 : 404);
    }

    public function deleteMappingByUserId(Request $request, int $userId): JsonResponse
    {
        $request->user()->can('user-package-group-delete-mapping');

        $list = UserPackageGroup::findByUserId($userId);

        $deleted = [];

        foreach($list as $userPackageGroup){
            $deleted[] = $userPackageGroup->toArray();
            $userPackageGroup->delete();
        }

        return new JsonResponse([
            'count' => count($deleted),
            'deleted' => $deleted,
        ], count($deleted) ? 200 : 404);
    }

    public function deleteMappingByPackageGroupId(Request $request, int $groupId): JsonResponse
    {
        $request->user()->can('user-package-group-delete-mapping');

        $list = UserPackageGroup::findByPackageGroupId($groupId);

        $deleted = [];

        foreach($list as $userPackageGroup){
            $deleted[] = $userPackageGroup->toArray();
            $userPackageGroup->delete();
        }

        return new JsonResponse([
            'count' => count($deleted),
            'deleted' => $deleted,
        ], count($deleted) ? 200 : 404);
    }
}
