<?php

use App\Model\PackageGroup;
use Illuminate\Database\Seeder;

class InitialPackageGroups extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PackageGroup::create(['name' => 'public']);
    }
}
