<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCapabilityMapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capability_map', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('entity_type');
            $table->integer('entity_id');
            $table->integer('capability_id');
            $table->foreign('capability_id')->references('id')->on('capability');
            $table->jsonb('constraint')->nullable();
            $table->unique(['entity_type', 'entity_id', 'capability_id', 'constraint']);
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
        Schema::dropIfExists('capability_map');
    }
}
