<?php

use App\Model\UserCapability;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RepoRangler\Interfaces\UserCapabilityInterface;

class InitialUserCapabilities extends Seeder
{
    const TABLE_NAME = 'capability';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(self::TABLE_NAME)->insert([
            ['name' => UserCapability::IS_ADMIN_USER, 'created_at' => 'now()'],
            ['name' => UserCapability::IS_REST_USER, 'created_at' => 'now()'],
            ['name' => UserCapability::PACKAGE_GROUP_ADMIN, 'created_at' => 'now()'],
            ['name' => UserCapability::PACKAGE_GROUP_ACCESS, 'created_at' => 'now()'],
            ['name' => UserCapability::REPOSITORY_ACCESS, 'created_at' => 'now()'],
        ]);
    }

    static public function getCapabilityId($name)
    {
        return DB::table(self::TABLE_NAME)->where('name', $name)->get(['id'])->first();
    }
}
