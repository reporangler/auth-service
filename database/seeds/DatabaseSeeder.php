<?php

use Illuminate\Database\Seeder;

//die(file_get_contents(__DIR__."/InitialAdminUser.php"));

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            InitialUserCapabilities::class,
            InitialAdminUser::class,
        ]);
    }
}
