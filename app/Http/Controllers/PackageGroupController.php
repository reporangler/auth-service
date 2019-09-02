<?php

namespace App\Http\Controllers;

use App\Model\PackageGroup;
use App\Services\DatabaseAuthenticator;
use App\Services\LDAPAuthenticator;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class PackageGroupController extends BaseController
{
    public function findByName(string $name): JsonResponse
    {
        return new JsonResponse(PackageGroup::where('name', $name)->firstOrFail(),200);
    }

    public function findById(int $id): JsonResponse
    {
        return new JsonResponse(PackageGroup::findOrFail($id),200);
    }

    public function getList(): JsonResponse
    {
        $list = PackageGroup::all();

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

        $result = PackageGroup::where('name', $data['name'])->first();

        if(!empty($result)){
            throw new UnprocessableEntityHttpException("Package Group '{$data['name']}' already exists");
        }

        $packageGroup = new PackageGroup();
        $packageGroup->name = $data['name'];
        $packageGroup->save();

        return new JsonResponse($packageGroup, 200);
    }

    public function update(Request $request): JsonResponse
    {
        $authSchema = [
            'id' => 'required|integer|min:1',
            'name' => 'required|string',
        ];

        $data = $this->validate($request,$authSchema);

        $packageGroup = PackageGroup::findOrFail($data['id']);
        $packageGroup->name = $data['name'];
        $packageGroup->save();

        return new JsonResponse($packageGroup, 200);
    }

    public function deleteById(int $id): JsonResponse
    {
        $packageGroup = PackageGroup::findOrFail($id);

        $deleted[] = $packageGroup->toArray();

        $packageGroup->delete();

        return new JsonResponse(['deleted' => $deleted], 200);
    }
}
