<?php

namespace App\Http\Controllers;

use App\Model\Capability;
use App\Model\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    public function giveAdmin(Request $request, int $userId): JsonResponse
    {
        Gate::allows('is-admin');

        /** @var User $user */
        $user = User::findOrFail($userId);
        if($user->hasCapability(Capability::IS_ADMIN_USER)){
            throw new BadRequestHttpException('This user already has admin permissions');
        }

        $user->giveAdmin();

        return new JsonResponse($user->refresh(), 201);
    }

    public function removeAdmin(Request $request, int $userId): JsonResponse
    {
        Gate::allows('is-admin');

        // We can't remove admin if the user doesn't have admin permissions
        try{
            /** @var User $user */
            $user = User::admin()->findOrFail($userId);
        }catch(ModelNotFoundException $e){
            throw new BadRequestHttpException('This user does not have admin permissions');
        }

        $list = User::admin();

        // You cannot remove admin permissions from the last admin user
        if($list->count() > 1) {
            $user->removeAdmin();

            return new JsonResponse(null, 204);
        }

        throw new BadRequestHttpException('You cannot delete the last remaining Administration User');
    }
}
