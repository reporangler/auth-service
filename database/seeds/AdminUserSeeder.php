<?php

use App\Model\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // TODO: can we use models like this?
        /*
        $user = new User();
        $user->setPassword('admin');
        $password = $user->getPassword();
        */

        DB::table('users')->insert([
            'username' => 'admin',
            'password' => password_hash('admin', PASSWORD_BCRYPT),
            'created_at' => 'NOW()',
        ]);
    }
}
