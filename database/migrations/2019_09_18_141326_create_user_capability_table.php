<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCapabilityTable extends Migration
{
    const USER_CAPABILITY_TABLE = 'user_capability';
    const CAPABILITY_TABLE = 'capability';
    const USER_TABLE = 'user';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::USER_CAPABILITY_TABLE, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('capability_id');
            $table->foreign('capability_id')->references('id')->on(self::CAPABILITY_TABLE);
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on(self::USER_TABLE);
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
        Schema::dropIfExists(self::USER_CAPABILITY_TABLE);
    }
}
