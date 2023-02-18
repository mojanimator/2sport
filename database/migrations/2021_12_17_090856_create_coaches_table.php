<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coaches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->smallInteger('county_id')->unsigned();
            $table->smallInteger('province_id')->unsigned();
            $table->smallInteger('sport_id')->unsigned();
            $table->string('name', 100);
            $table->string('family', 100);
            $table->dateTime('born_at');

            $table->boolean('is_man'); //man:true woman:false
            $table->boolean('active')->default(false);
            $table->boolean('hidden')->default(false);
            $table->string('phone', 50)->nullable();
            $table->string('description', 2048)->nullable(); //resume

            $table->timestamps();
            $table->timestamp('expires_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('sport_id')->references('id')->on('sports')->onDelete('no action');
            $table->foreign('county_id')->references('id')->on('county')->onDelete('no action');
            $table->foreign('province_id')->references('id')->on('province')->onDelete('no action');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coaches');
    }
}
