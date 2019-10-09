<?php

namespace App\Http\Controllers;

use App\Model\Capability;
use App\Model\PackageGroup;
use App\Model\User;
use App\Model\UserCapability;
use App\Model\UserPackageGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class CapabilityController extends BaseController
{
    public function joinPackageGroup(Request $request)
    {
        $data = $this->validate($request, [
            'user_id' => 'required|integer|min:1',
            'package_group_id' => 'required|integer|min:1',
            'access' => 'boolean',
            'admin' => 'boolean',
        ]);

        $user = User::findOrFail($data['user_id']);
        $packageGroup = PackageGroup::findOrFail($data['package_group_id']);

        $created = [];
        $exists = [];

        $capability = UserPackageGroup::whereUserHasPackageGroup($user, $packageGroup, Capability::PACKAGE_GROUP_ACCESS)->first();
        if($capability === null){
            $capability = new UserCapability([
                'name' => Capability::PACKAGE_GROUP_ACCESS,
                'user_id' => $user->id,
                'constraint' => [
                    'name' => $packageGroup->name
                ]
            ]);
            $capability->save();
            $created[] = $capability;
        }else{
            $exists[] = $capability;
        }

        $capability = UserPackageGroup::whereUserHasPackageGroup($user, $packageGroup, Capability::PACKAGE_GROUP_ADMIN)->first();
        if($capability === null){
            $capability = new UserCapability([
                'name' => Capability::PACKAGE_GROUP_ADMIN,
                'user_id' => $user->id,
                'constraint' => [
                    'name' => $packageGroup->name
                ]
            ]);
            $capability->save();
            $created[] = $capability;
        }else{
            $exists[] = $capability;
        }

        return new JsonResponse(['created' => $created, 'exists' => $exists]);
    }

    public function leavePackageGroup(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__]);
    }

    public function requestJoinPackageGroup(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__]);
    }

    public function joinRepository(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__]);
    }

    public function leaveRepository(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__]);
    }

    public function requestJoinRepository(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__]);
    }

    public function protectPackageGroup(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__]);
    }

    public function unprotectPackageGroup(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__]);
    }

    public function protectRepository(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__]);
    }

    public function unprotectRepository(Request $request)
    {
        return new JsonResponse(['method' => __METHOD__]);
    }
}
