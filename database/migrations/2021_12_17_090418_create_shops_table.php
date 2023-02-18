<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->smallInteger('county_id')->unsigned();
            $table->smallInteger('province_id')->unsigned();
            $table->string('name', 100);
            $table->string('address', 1024);
            $table->string('phone', 50)->nullable();
            $table->string('description', 1024)->nullable();
            $table->boolean('active')->default(false);
            $table->boolean('hidden')->default(false);
            $table->string('location', 100)->nullable();
            $table->json('groups')->nullable();

            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('shops');
    }
}
