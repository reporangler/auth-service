<?php

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
        $user               = new User();
        $user->password     = env('APP_ADMIN_DEFAULT_PASSWORD');

        $id = DB::table('user')->insertGetId([
            'username'      => env('APP_ADMIN_DEFAULT_USERNAME'),
            'email'         => env('APP_ADMIN_DEFAULT_EMAIL'),
            'password'      => $user->password,
            'created_at'    => 'NOW()',
        ]);

        self::setCapability($id, UserCapability::IS_ADMIN_USER);
        self::setCapability($id, UserCapability::IS_REST_USER);
    }

    static public function setCapability($userId, $capabilityName)
    {
        $cap = InitialUserCapabilities::getCapabilityId($capabilityName);

        DB::table('user_capability')->insert([
            'capability_id' => $cap->id,
            'user_id' => $userId,
            'created_at' => 'NOW()',
        ]);
    }
}
