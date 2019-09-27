<?php

use App\Model\Capability;
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
        $user = User::create([
            'username' => env('APP_ADMIN_DEFAULT_USERNAME'),
            'email'    => env('APP_ADMIN_DEFAULT_EMAIL'),
            'password' => env('APP_ADMIN_DEFAULT_PASSWORD'),
        ]);
        $user->save();
        $user->capability()->saveMany([
            new UserCapability(['name' => Capability::IS_ADMIN_USER]),
            new UserCapability(['name' => Capability::IS_REST_USER]),
        ]);
    }
}
