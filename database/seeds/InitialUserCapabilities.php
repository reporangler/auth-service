<?php

use App\Model\Capability;
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
        Capability::create(['name' => Capability::IS_ADMIN_USER]);
        Capability::create(['name' => Capability::IS_REST_USER]);
        Capability::create(['name' => Capability::PACKAGE_GROUP_ADMIN]);
        Capability::create(['name' => Capability::PACKAGE_GROUP_ACCESS]);
        Capability::create(['name' => Capability::REPOSITORY_ACCESS]);
    }
}
