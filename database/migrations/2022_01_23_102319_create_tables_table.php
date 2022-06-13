<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('tournament_id')->unsigned()->nullable();
            $table->string('title', 100);
            $table->string('tags', 150)->nullable();
//            $table->tinyInteger('type_id')->unsigned(); //Helper::$tableTypes()
            $table->json('content');
            $table->boolean('active')->default(true);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tables');
    }
}
