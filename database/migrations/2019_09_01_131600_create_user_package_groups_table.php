<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPackageGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_package_groups', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->integer('package_group_id');
            $table->unique(['user_id', 'package_group_id']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('package_group_id')->references('id')->on('package_groups');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_package_groups');
    }
}
