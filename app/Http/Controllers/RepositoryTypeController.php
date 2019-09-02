<?php

namespace App\Http\Controllers;

use App\Model\RepositoryType;
use App\Services\DatabaseAuthenticator;
use App\Services\LDAPAuthenticator;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class RepositoryTypeController extends BaseController
{
    public function findByName(string $name): JsonResponse
    {
        return new JsonResponse(RepositoryType::where('name', $name)->firstOrFail(),200);
    }

    public function findById(int $id): JsonResponse
    {
        return new JsonResponse(RepositoryType::findOrFail($id),200);
    }

    public function getList(): JsonResponse
    {
        $list = RepositoryType::all();

        return new JsonResponse([
            'count' => count($list),
            'data' => $list
        ], count($list) ? 200 : 404);
    }

    public function create(Request $request): JsonResponse
    {
        $authSchema = [
            'name' => 'required|string',
        ];

        $data = $this->validate($request,$authSchema);

        $result = RepositoryType::where('name', $data['name'])->first();

        if(!empty($result)){
            throw new UnprocessableEntityHttpException("Repository Type '{$data['name']}' already exists");
        }

        $repositoryType = new RepositoryType();
        $repositoryType->name = $data['name'];
        $repositoryType->save();

        return new JsonResponse($repositoryType, 200);
    }

    public function update(Request $request): JsonResponse
    {
        $authSchema = [
            'id' => 'required|integer|min:1',
            'name' => 'required|string',
        ];

        $data = $this->validate($request,$authSchema);

        $repositoryType = RepositoryType::findOrFail($data['id']);
        $repositoryType->name = $data['name'];
        $repositoryType->save();

        return new JsonResponse($repositoryType, 200);
    }

    public function deleteById(int $id): JsonResponse
    {
        $repositoryType = RepositoryType::findOrFail($id);

        $deleted[] = $repositoryType->toArray();

        $repositoryType->delete();

        return new JsonResponse(['deleted' => $deleted], 200);
    }
}
