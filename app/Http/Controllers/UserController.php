<?php

namespace App\Http\Controllers;

use App\Model\PackageGroup;
use App\Model\User;
use App\Model\UserPackageGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class UserController extends BaseController
{
    /**
     * @param string $username
     * @return JsonResponse
     */
    public function findByUsername(string $username): JsonResponse
    {
        return new JsonResponse(User::where('username', $username)->firstOrFail());
    }

    /**
     * @param int $userId
     * @return JsonResponse
     */
    public function findById(int $userId): JsonResponse
    {
        return new JsonResponse(User::findOrFail($userId));
    }

    /**
     * @return JsonResponse
     */
    public function getList(): JsonResponse
    {
        $list = User::get();

        return new JsonResponse([
            'count' => count($list),
            'data' => $list
        ], count($list) ? 200 : 404);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        $schema = [
            'username'  => 'required|string',
            'email'     => 'required|email',
            'password'  => 'required|string|min:8'
        ];

        $data = $this->validate($request,$schema);

        // Find a user with this same data
        $result = User::where([
            'username'  => $data['username'],
            'email'     => $data['email'],
            'password'  => $data['password']
        ])->first();

        if(!empty($result)){
            throw new UnprocessableEntityHttpException('User already exists');
        }

        // Otherwise, create a new user
        $user = new User();
        $user->username = $data['username'];
        $user->email    = $data['email'];
        $user->password = $data['password'];
        $user->save();

        return new JsonResponse($user);
    }

    /**
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $userId): JsonResponse
    {
        $request->user()->can('user-update');

        $schema = [
            'username'  => 'string',
            'email'     => 'email',
            'password'  => 'string|min:8',
        ];

        $data = $this->validate($request,$schema);

        $user = User::findOrFail($userId);

        foreach($schema as $key => $value){
            if(array_key_exists($key, $data)){
                $user->$key = $data[$key];
            }
        }

        $user->save();

        return new JsonResponse($user);
    }

    /**
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function deleteById(Request $request, int $userId): JsonResponse
    {
        $request->user()->can('user-delete');

        $user = User::findOrFail($userId);

        $deleted[] = $user->toArray();

        $user->delete();

        return new JsonResponse(['deleted' => $deleted]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createPackageGroupMapping(Request $request): JsonResponse
    {
        $request->user()->can('user-package-group-create-mapping');

        $authSchema = [
            'user_id' => 'required|int|min:1',
            'package_group_id' => 'required|int|min:1',
        ];

        $data = $this->validate($request,$authSchema);

        $user = User::findOrFail($data['user_id']);
        $packageGroup = PackageGroup::findOrFail($data['package_group_id']);

        $userPackageGroup = UserPackageGroup::whereUserHasPackageGroup($user, $packageGroup)->first();

        if($userPackageGroup){
            throw new UnprocessableEntityHttpException("User Package Group with '{$user->username} (id: {$user->id})' and '{$packageGroup->name} (id: {$packageGroup->id})' already exists");
        }

        $userPackageGroup = UserPackageGroup::create($packageGroup, $user);

        return new JsonResponse($userPackageGroup);
    }

    /**
     * @param Request $request
     * @param int $userId
     * @param int $groupId
     * @return JsonResponse
     * @throws \Exception
     */
    public function deletePackageGroupMapping(Request $request, int $userId, int $groupId): JsonResponse
    {
        $request->user()->can('user-package-group-delete-mapping');

        $user = User::findOrFail($userId);
        $packageGroup = PackageGroup::findOrFail($groupId);

        $userPackageGroup = UserPackageGroup::whereUserHasPackageGroup($user, $packageGroup)->first();

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
}
