<?php

namespace App\Http\Controllers;

use App\Model\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller as BaseController;

class UserController extends BaseController
{
    /**
     * @param string $username
     * @return JsonResponse
     */
    public function findByUsername(string $username): JsonResponse
    {
        return new JsonResponse(User::where('username', $username)->firstOrFail(),200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function findById(int $id): JsonResponse
    {
        return new JsonResponse(User::findOrFail($id),200);
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

        return new JsonResponse($user, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->user()->can('user-update');

        $schema = [
            'username'  => 'string',
            'email'     => 'email',
            'password'  => 'string|min:8',
        ];

        $data = $this->validate($request,$schema);

        $user = User::findOrFail($id);

        foreach($schema as $key => $value){
            if(array_key_exists($key, $data)){
                $user->$key = $data[$key];
            }
        }

        $user->save();

        return new JsonResponse($user, 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function deleteById(int $id): JsonResponse
    {
        $request->user()->can('user-delete');

        $user = User::findOrFail($id);

        $deleted[] = $user->toArray();

        $user->delete();

        return new JsonResponse(['deleted' => $deleted], 200);
    }
}
