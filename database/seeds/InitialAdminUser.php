<?php

use App\Services\RepositoryService;
use App\Services\PackageGroupService;
use App\Model\Capability;
use App\Model\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialAdminUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'username' => env('APP_ADMIN_DEFAULT_USERNAME'),
            'email'    => env('APP_ADMIN_DEFAULT_EMAIL'),
            'password' => env('APP_ADMIN_DEFAULT_PASSWORD'),
        ]);

        $user->capability()->saveMany([
            new CapabilityMap(['name' => Capability::IS_ADMIN_USER]),
            new CapabilityMap(['name' => Capability::IS_REST_USER]),
        ]);
    }
}
