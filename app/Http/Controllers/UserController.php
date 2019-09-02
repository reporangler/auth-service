<?php

namespace App\Http\Controllers;

use App\Services\DatabaseAuthenticator;
use App\Services\LDAPAuthenticator;
use App\Model\User;
use App\Model\RepositoryType;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends BaseController
{
    public function login(Request $request)
    {
        $authSchema = [
            'type' => 'required|in:http-basic,ldap',
            'username' => 'required|string',
            'password' => 'required|string',
        ];

        $data = $this->validate($request,$authSchema);

        switch($data['type']){
            case 'http-basic':
                $db = app(DatabaseAuthenticator::class);
                return new JsonResponse($db->auth($data['username'], $data['password']), 200);
                break;

            case 'ldap':
                $ldap = app(LDAPAuthenticator::class);
                return new JsonResponse($ldap->auth($data['username'], $data['password']), 200);
                break;
        }

        throw new BadRequestHttpException("No Authorization attempt made");
    }

    public function check()
    {
        return new JsonResponse([__METHOD__], 200);
    }

    public function findByUsername(string $username): JsonResponse
    {
        return new JsonResponse(User::where('username', $username)->firstOrFail(),200);
    }

    public function findById(int $id): JsonResponse
    {
        return new JsonResponse(User::findOrFail($id),200);
    }

    public function getList(): JsonResponse
    {
        $list = User::all();

        return new JsonResponse([
            'count' => count($list),
            'data' => $list
        ], count($list) ? 200 : 404);
    }

    public function create(Request $request): JsonResponse
    {
        $authSchema = [
            'username' => 'required|string',
            'password' => 'required|string|min:8',
            'repository_type' => 'required|string'
        ];

        $data = $this->validate($request,$authSchema);

        // The repository type must exist
        $repositoryType = RepositoryType::where('name', $data['repository_type'])->firstOrFail();

        // Find a user with this same data
        $result = User::where([
            'username' => $data['username'],
            'password' => $data['password'],
            'repository_type_id' => $repositoryType->id,
        ])->first();

        if(!empty($result)){
            throw new UnprocessableEntityHttpException("User already exists");
        }

        // Otherwise, create a new user
        $user = new User();
        $user->username = $data['username'];
        $user->password = sha1($data['password']);
        $user->repository_type_id = $repositoryType->id;
        $user->save();

        return new JsonResponse($user, 200);
    }

    public function update(Request $request): JsonResponse
    {
        $schema = [
            'id' => 'required|integer|min:1',
            'username' => 'string',
            'password' => 'string|min:8',
            'repository_type' => 'string'
        ];

        $data = $this->validate($request,$schema);

        $user = User::findOrFail($data['id']);

        if(array_key_exists('repository_type', $data)){
            // The repository type must exist
            $repositoryType = RepositoryType::where('name', $data['repository_type'])->firstOrFail();
            $user->repository_type_id = $repositoryType->id;
        }

        if(array_key_exists('username', $data)){
            $user->username = $data['username'];
        }

        if(array_key_exists('password', $data)){
            $user->password = sha1($data['password']);
        }

        $user->save();

        return new JsonResponse($user, 200);
    }

    public function deleteById(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $deleted[] = $user->toArray();

        $user->delete();

        return new JsonResponse(['deleted' => $deleted], 200);
    }
}