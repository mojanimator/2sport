<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->bigInteger('owner_id')->unsigned()->nullable();
            $table->string('name', 100);
            $table->string('phone', 50)->nullable();
            $table->boolean('active')->default(true);
            $table->string('location', 100)->nullable();
            $table->string('email', 50)->unique()->nullable();
            $table->string('address', 1024)->nullable();
            $table->string('description', 1024)->nullable();
            $table->smallInteger('province_id')->unsigned()->nullable();
            $table->smallInteger('county_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('agencies')->onDelete('set null');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
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
        Schema::dropIfExists('agencies');
    }
}
