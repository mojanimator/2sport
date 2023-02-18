<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('sport_id')->index()->unsigned()->nullable();
            $table->bigInteger('user_id')->index()->unsigned()->nullable();
            $table->string('title', 150);
            $table->string('team1', 100)->nullable();
            $table->string('team2', 100)->nullable();
            $table->string('score1', 10)->nullable();
            $table->string('score2', 10)->nullable();
            $table->enum('status', Helper::$eventStatus)->nullable();
            $table->string('source', 100)->nullable();
            $table->string('link', 100)->nullable();
            $table->string('details', 2048)->nullable();
            $table->timestamp('time')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
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
        Schema::dropIfExists('events');
    }
}
