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

        $capability = UserToPackageGroup::where($user, $packageGroup, $repository, Capability::PACKAGE_GROUP_ACCESS)->first();
        if($capability === null){
            $created[] = UserToPackageGroup::create(Capability::PACKAGE_GROUP_ACCESS, $user, $packageGroup, $repository);
        }else{
            $exists[] = $capability;
        }

        if(array_key_exists('admin', $data) && $data['admin'] === true){
            $capability = UserToPackageGroup::where($user, $packageGroup, $repository, Capability::PACKAGE_GROUP_ADMIN)->first();
            if($capability === null){
                $created[] = UserToPackageGroup::create(Capability::PACKAGE_GROUP_ADMIN, $user, $packageGroup, $repository);
            }else{
                $exists[] = $capability;
            }
        }

        return new JsonResponse(['created' => $created, 'exists' => $exists]);
    }

    public function leavePackageGroup(Request $request)
    {
        $data = $this->validate($request, [
            'user_id' => 'required|integer|min:1',
            'package_group_id' => 'required|integer|min:1',
        ]);

        $user = User::findOrFail($data['user_id']);
        $packageGroup = PackageGroup::findOrFail($data['package_group_id']);

        $deleted = [];

        $tests = [Capability::PACKAGE_GROUP_ACCESS, Capability::PACKAGE_GROUP_ADMIN];

        foreach($tests as $permission){
            $capability = UserToPackageGroup::where($user, $packageGroup, $permission)->first();
            if($capability instanceOf UserCapability) {
                $deleted[] = $capability->toArray();
                $capability->delete();
            }
        }

        return new JsonResponse(['deleted' => $deleted]);
    }

    public function requestJoinPackageGroup(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function joinRepository(Request $request)
    {
        $data = $this->validate($request, [
            'user_id' => 'required|integer|min:1',
            'repository_id' => 'required|string',
            'access' => 'boolean',
            'admin' => 'boolean',
        ]);

        $user = User::findOrFail($data['user_id']);
        $repository = new Repository(['name' => $data['repository_id']]);

        $created = [];
        $exists = [];

        $tests = [Capability::REPOSITORY_ACCESS, Capability::REPOSITORY_ADMIN];

        foreach($tests as $permission){
            $capability = UserToRepository::where($user, $repository, $permission)->first();
            if($capability === null){
                $created[] = UserToRepository::create($permission, $user, $repository);
            }else{
                $exists[] = $capability;
            }
        }

        return new JsonResponse(['created' => $created, 'exists' => $exists]);
    }

    public function leaveRepository(Request $request)
    {
        $data = $this->validate($request, [
            'user_id' => 'required|integer|min:1',
            'repository_id' => 'required|string',
        ]);

        $user = User::findOrFail($data['user_id']);
        $repository = new Repository(['name' => $data['repository_id']]);

        $deleted = [];

        $tests = [Capability::REPOSITORY_ACCESS, Capability::REPOSITORY_ADMIN];

        foreach($tests as $permission){
            $capability = UserToRepository::where($user, $repository, $permission)->first();
            if($capability instanceOf UserCapability) {
                $deleted[] = $capability->toArray();
                $capability->delete();
            }
        }

        return new JsonResponse(['deleted' => $deleted]);
    }

    public function requestJoinRepository(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function protectPackageGroup(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function unprotectPackageGroup(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function protectRepository(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }

    public function unprotectRepository(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__, 'todo' => 'at the moment only users can have permissions']);
    }
}
