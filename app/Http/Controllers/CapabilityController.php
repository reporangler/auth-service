<?php

namespace App\Http\Controllers;

use App\Meta\UserToPackageGroup;
use App\Meta\UserToRepository;
use App\Model\Capability;
use App\Model\User;
use App\Model\UserCapability;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;
use RepoRangler\Entity\Repository;
use RepoRangler\Services\MetadataClient;

class CapabilityController extends BaseController
{
    public function joinPackageGroup(Request $request, MetadataClient $metadataClient)
    {
        $login = Auth::guard('token')->user();

        $data = $this->validate($request, [
            'user_id' => 'required|integer|min:1',
            'package_group_id' => 'required|integer|min:1',
            'repository_id' => 'required|integer|min:1',
            'access' => 'required_without:admin|boolean',
            'admin' => 'required_without:access|boolean',
        ]);

        $user = User::findOrFail($data['user_id']);
        $packageGroup = $metadataClient->getPackageGroupById($login->token, $data['package_group_id']);
        $repository = $metadataClient->getRepositoryById($login->token, $data['repository_id']);

        $created = [];
        $exists = [];

        $permissions = array_key_exists('admin', $data) && $data['admin'] === true
            ? [Capability::PACKAGE_GROUP_ACCESS, Capability::PACKAGE_GROUP_ADMIN]
            : [Capability::PACKAGE_GROUP_ACCESS];

        foreach($permissions as $p){
            $capability = UserToPackageGroup::where($user, $packageGroup, $repository, $p)->first();
            if($capability === null){
                $created[] = UserToPackageGroup::create($p, $user, $packageGroup, $repository);
            }else{
                $exists[] = $capability;
            }
        }

        return new JsonResponse(['created' => $created, 'exists' => $exists]);
    }

    public function leavePackageGroup(Request $request, MetadataClient $metadataClient)
    {
        $login = Auth::guard('token')->user();

        $data = $this->validate($request, [
            'user_id' => 'required|integer|min:1',
            'package_group_id' => 'required|integer|min:1',
            'repository_id' => 'required|integer|min:1',
        ]);

        $user = User::findOrFail($data['user_id']);
        $packageGroup = $metadataClient->getPackageGroupById($login->token, $data['package_group_id']);
        $repository = $metadataClient->getRepositoryById($login->token, $data['repository_id']);

        $deleted = [];

        $permissions = [Capability::PACKAGE_GROUP_ACCESS, Capability::PACKAGE_GROUP_ADMIN];

        foreach($permissions as $p){
            $capability = UserToPackageGroup::where($user, $packageGroup, $repository, $p)->first();
            if($capability instanceOf UserCapability) {
                $deleted[] = $capability->toArray();
                $capability->delete();
            }
        }

        return new JsonResponse(['deleted' => $deleted]);
    }

    public function requestJoinPackageGroup(Request $request, MetadataClient $metadataClient)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function joinRepository(Request $request, MetadataClient $metadataClient)
    {
        $login = Auth::guard('token')->user();

        $data = $this->validate($request, [
            'user_id' => 'required|integer|min:1',
            'repository_id' => 'required|integer|min:1',
            'access' => 'required_without:admin|boolean',
            'admin' => 'required_without:access|boolean',
        ]);

        $user = User::findOrFail($data['user_id']);
        $repository = $metadataClient->getRepositoryById($login->token, $data['repository_id']);

        $created = [];
        $exists = [];

        $permissions = array_key_exists('admin', $data) && $data['admin'] === true
            ? [Capability::REPOSITORY_ACCESS, Capability::REPOSITORY_ADMIN]
            : [Capability::REPOSITORY_ACCESS];

        foreach($permissions as $p){
            $capability = UserToRepository::where($user, $repository, $p)->first();
            if($capability === null){
                $created[] = UserToRepository::create($p, $user, $repository);
            }else{
                $exists[] = $capability;
            }
        }

        return new JsonResponse(['created' => $created, 'exists' => $exists]);
    }

    public function leaveRepository(Request $request, MetadataClient $metadataClient)
    {
        $login = Auth::guard('token')->user();

        $data = $this->validate($request, [
            'user_id' => 'required|integer|min:1',
            'repository_id' => 'required|integer|min:1',
        ]);

        $user = User::findOrFail($data['user_id']);
        $repository = $metadataClient->getRepositoryById($login->token, $data['repository_id']);

        $deleted = [];

        $permissions = [Capability::REPOSITORY_ACCESS, Capability::REPOSITORY_ADMIN];

        foreach($permissions as $p){
            $capability = UserToRepository::where($user, $repository, $p)->first();
            if($capability instanceOf UserCapability) {
                $deleted[] = $capability->toArray();
                $capability->delete();
            }
        }

        return new JsonResponse(['deleted' => $deleted]);
    }

    public function requestJoinRepository(Request $request, MetadataClient $metadataClient)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function protectPackageGroup(Request $request, MetadataClient $metadataClient)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function unprotectPackageGroup(Request $request, MetadataClient $metadataClient)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function protectRepository(Request $request, MetadataClient $metadataClient)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function unprotectRepository(Request $request, MetadataClient $metadataClient)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }
}
