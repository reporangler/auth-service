<?php

use App\Model\Capability;
use App\Model\PackageGroup;
use App\Model\User;
use App\Model\UserCapability;
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
        $username = env('APP_ADMIN_DEFAULT_USERNAME');
        $packageGroup = "$username-user";

        PackageGroup::create(['name' => $packageGroup]);

        $user = User::create([
            'username' => $username,
            'email'    => env('APP_ADMIN_DEFAULT_EMAIL'),
            'password' => env('APP_ADMIN_DEFAULT_PASSWORD'),
        ]);
        $user->save();

        $user->capability()->saveMany([
            new UserCapability(['name' => Capability::IS_ADMIN_USER]),
            new UserCapability(['name' => Capability::IS_REST_USER]),
            new UserCapability(['name' => Capability::PACKAGE_GROUP_ADMIN, 'constraint' => ['name' => 'public']]),
            new UserCapability(['name' => Capability::PACKAGE_GROUP_ADMIN, 'constraint' => ['name' => $packageGroup]]),
            new UserCapability(['name' => Capability::PACKAGE_GROUP_ACCESS, 'constraint' => ['name' => $packageGroup]]),
            new UserCapability(['name' => Capability::REPOSITORY_ADMIN]),
            new UserCapability(['name' => Capability::REPOSITORY_ACCESS, 'constraint' => ['name' => 'php']]),
            new UserCapability(['name' => Capability::REPOSITORY_ACCESS, 'constraint' => ['name' => 'npm']]),
        ]);
    }
}
