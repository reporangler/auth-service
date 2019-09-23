<?php

namespace App\Http\Controllers;

use App\Model\UserPackageGroup;
use App\Services\DatabaseAuthenticator;
use App\Services\LDAPAuthenticator;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UserPackageGroupController extends BaseController
{
    public function findByUserId(int $id): JsonResponse
    {
        return new JsonResponse(UserPackageGroup::where('user_id', $id)->firstOrFail(),200);
    }

    public function findByPackageGroupId(int $id): JsonResponse
    {
        return new JsonResponse(UserPackageGroup::where('package_group_id', $id)->firstOrFail(),200);
    }

    public function getList(): JsonResponse
    {
        $list = UserPackageGroup::all();

        return new JsonResponse([
            'count' => count($list),
            'data' => $list
        ], count($list) ? 200 : 404);
    }

    public function create(Request $request): JsonResponse
    {
        $request->user()->can('user-package-group-create-mapping');

        $authSchema = [
            'user_id' => 'required|int|min:1',
            'package_group_id' => 'required|int|min:1',
        ];

        $data = $this->validate($request,$authSchema);

        $userPackageGroup = UserPackageGroup::where([
            'user_id' => $data['user_id'],
            'package_group_id' => $data['package_group_id'],
        ])->first();

        if($userPackageGroup){
            throw new UnprocessableEntityHttpException("User Package Group with '{$data['user_id']}' and '{$data['package_group_id']}' already exists");
        }

        $userPackageGroup = new UserPackageGroup();
        $userPackageGroup->user_id = $data['user_id'];
        $userPackageGroup->package_group_id = $data['package_group_id'];
        $userPackageGroup->save();

        return new JsonResponse($userPackageGroup, 200);
    }

    public function deleteMapping(int $userId, int $groupId): JsonResponse
    {
        $request->user()->can('user-package-group-delete-mapping');

        $userPackageGroup = UserPackageGroup::where([
            'user_id' => $userId,
            'package_group_id' => $groupId,
        ]);

        $deleted = [];

        foreach($userPackageGroup as $u){
            $deleted[] = $u->toArray();
            $u->delete();
        }

        return new JsonResponse(['deleted' => $deleted], 200);
    }

    public function deleteByUserId(int $id): JsonResponse
    {
        $request->user()->can('user-package-group-delete-mapping');

        $userPackageGroup = UserPackageGroup::where('user_id', $id);

        $deleted = [];

        foreach($userPackageGroup as $u){
            $deleted[] = $u->toArray();
            $u->delete();
        }

        return new JsonResponse(['deleted' => $deleted], 200);
    }

    public function deleteByPackageGroupId(int $id): JsonResponse
    {
        $request->user()->can('user-package-group-delete-mapping');

        $userPackageGroup = UserPackageGroup::where('package_group_id', $id);

        $deleted = [];

        foreach($userPackageGroup as $u){
            $deleted[] = $u->toArray();
            $u->delete();
        }

        return new JsonResponse(['deleted' => $deleted], 200);
    }
}
