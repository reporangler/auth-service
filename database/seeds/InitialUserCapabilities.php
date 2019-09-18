<?php

use Illuminate\Database\Seeder;

class InitialUserCapabilities extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_capability')->insert([
            ['name' => 'IS_ADMIN', 'created_at' => 'now()'],
            ['name' => 'REST_API', 'created_at' => 'now()'],
            ['name' => 'PACKAGE_GROUP_ADMIN', 'created_at' => 'now()'],
            ['name' => 'PACKAGE_GROUP_ACCESS', 'created_at' => 'now()'],
            ['name' => 'REPOSITORY_ACCESS', 'created_at' => 'now()'],
        ]);
    }
}
