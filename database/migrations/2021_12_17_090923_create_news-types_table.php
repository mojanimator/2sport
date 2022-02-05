<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateNewsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news-types', function (Blueprint $table) {
            $table->tinyIncrements('id')->unsigned();
            $table->string('name', 50);
        });


        DB::table('news-types')->insert([
            ['id' => 1, 'name' => 'فوتبال داخلی',],
            ['id' => 2, 'name' => 'فوتبال خارجی',],


        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news-types');
    }
}
