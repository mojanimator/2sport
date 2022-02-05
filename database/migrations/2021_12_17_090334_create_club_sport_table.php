<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubSportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_sport', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('club_id')->unsigned();
            $table->smallInteger('sport_id')->unsigned();
            $table->enum('gender', ['m', 'w']);
            $table->time('s');
            $table->time('e');

            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('no action');
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
        Schema::dropIfExists('club_sport');
    }
}
