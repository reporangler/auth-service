<?php

namespace App\Http\Controllers;

use App\Model\PackageGroup;
use App\Model\UserPackageGroup;
use App\Services\DatabaseAuthenticator;
use App\Services\LDAPAuthenticator;
use App\Model\User;
use App\Model\RepositoryType;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserController extends BaseController
{
    public function login(Request $request)
    {
        $authSchema = [
            'type' => 'required|in:http-basic,database,ldap',
            'username' => 'required|string',
            'password' => 'required|string',
            'repository_type' => 'required|string',
        ];

        $data = $this->validate($request,$authSchema);

        $user = User::where([
            'username' => $data['username'],
            'repository_type' => $data['repository_type'],
        ])->firstOrFail();

        switch($data['type']){
            case 'database':
            case 'http-basic':
                $auth = app(DatabaseAuthenticator::class);

                $user = $auth->login($user, $data['password']);
                break;

            case 'ldap':
                $ldap = app(LDAPAuthenticator::class);
                $user = $ldap->login($user, $data['password']);
                break;

            default:
                throw new BadRequestHttpException("No Authorization attempt made");
                break;
        }

        return new JsonResponse($user, 200);
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

        // Find a user with this same data
        $result = User::where([
            'username' => $data['username'],
            'password' => $data['password'],
            'repository_type' => $data['repository_type']
        ])->first();

        if(!empty($result)){
            throw new UnprocessableEntityHttpException("User already exists");
        }

        // Otherwise, create a new user
        $user = new User();
        $user->setUsername($data['username']);
        $user->setPassword($data['password']);
        $user->setRepositoryType($data['repository_type']);
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
            $user->setRepositoryType($data['repository_type']);
        }

        if(array_key_exists('username', $data)){
            $user->setUsername($data['username']);
        }

        if(array_key_exists('password', $data)){
            $user->setPassword($data['password']);
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
