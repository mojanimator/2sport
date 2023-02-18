<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSportRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sport-rules', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('name', 50);
            $table->smallInteger('sport_id')->unsigned();

            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sport-rules');

    }
}
