<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialUserCapabilities extends Seeder
{
    const TABLE_NAME = 'user_capability';
    const IS_ADMIN = 'IS_ADMIN';
    const REST_API = 'REST_API';
    const PACKAGE_GROUP_ADMIN = 'PACKAGE_GROUP_ADMIN';
    const PACKAGE_GROUP_ACCESS = 'PACKAGE_GROUP_ACCESS';
    const REPOSITORY_ACCESS = 'REPOSITORY_ACCESS';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(self::TABLE_NAME)->insert([
            ['name' => self::IS_ADMIN, 'created_at' => 'now()'],
            ['name' => self::REST_API, 'created_at' => 'now()'],
            ['name' => self::PACKAGE_GROUP_ADMIN, 'created_at' => 'now()'],
            ['name' => self::PACKAGE_GROUP_ACCESS, 'created_at' => 'now()'],
            ['name' => self::REPOSITORY_ACCESS, 'created_at' => 'now()'],
        ]);
    }

    static public function getCapabilityId($name)
    {
        return DB::table(self::TABLE_NAME)->where('name', $name)->get(['id'])->first();
    }
}
