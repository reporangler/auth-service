<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCapabilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_capability', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('capability_id');
            $table->foreign('capability_id')->references('id')->on('capability');
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('user');
            $table->jsonb('constraint')->nullable();
            $table->unique(['capability_id', 'user_id', 'constraint']);
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
        Schema::dropIfExists('user_capability');
    }
}
