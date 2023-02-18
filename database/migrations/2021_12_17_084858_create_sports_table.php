<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sports', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('name', 50);
        });

        DB::table('sports')->insert([
            ['id' => 1, 'name' => 'فوتبال',],
            ['id' => 2, 'name' => 'فوتسال'],
            ['id' => 3, 'name' => 'والیبال'],
            ['id' => 4, 'name' => 'بسکتبال'],
            ['id' => 5, 'name' => 'بدنسازی'],
            ['id' => 6, 'name' => 'شنا'],
            ['id' => 7, 'name' => 'کشتی'],
            ['id' => 8, 'name' => 'تکواندو'],
            ['id' => 9, 'name' => 'کاراته'],
            ['id' => 10, 'name' => 'ووشو'],
            ['id' => 11, 'name' => 'جودو'],
            ['id' => 12, 'name' => 'ژیمناستیک'],
            ['id' => 13, 'name' => 'ایروبیک'],
            ['id' => 14, 'name' => 'یوگا'],
            ['id' => 15, 'name' => 'بیلیارد'],

        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sports');
    }
}
