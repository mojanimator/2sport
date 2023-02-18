<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->smallInteger('county_id')->unsigned();
            $table->smallInteger('province_id')->unsigned();
            $table->string('name', 100);
            $table->string('address', 1024);
            $table->string('description', 1024)->nullable();
            $table->string('phone', 50)->nullable();
            $table->boolean('active')->default(false);
            $table->boolean('hidden')->default(false);
            $table->json('times');
            $table->string('location', 100)->nullable();
            $table->timestamps();
            $table->timestamp('expires_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
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
        Schema::dropIfExists('clubs');
    }
}
