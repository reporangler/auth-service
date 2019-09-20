<?php

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
        $user = new User();
        $user->setPassword('admin');

        $id = DB::table('users')->insertGetId([
            'username' => 'admin',
            'password' => $user->password,
            'created_at' => 'NOW()',
        ]);

        self::setCapability($id, InitialUserCapabilities::IS_ADMIN);
        self::setCapability($id, InitialUserCapabilities::REST_API);
    }

    static public function setCapability($userId, $capabilityName)
    {
        $cap = InitialUserCapabilities::getCapabilityId($capabilityName);

        DB::table('user_capability_map')->insert([
            'capability_id' => $cap->id,
            'user_id' => $userId,
            'created_at' => 'NOW()',
        ]);
    }
}
