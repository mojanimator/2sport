<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('invited_id')->unsigned()->index();
            $table->bigInteger('inviter_id')->unsigned()->index();
            //if invited make a purchase ->pay to inviters
//            $table->timestamp('invited_purchased_at')->nullable();
            $table->tinyInteger('invited_purchase_type')->unsigned()->nullable();
            $table->tinyInteger('invited_purchase_months')->unsigned()->nullable();
            $table->timestamp('payed_1_at')->nullable();
            $table->integer('payed_1')->unsigned()->nullable();
            $table->timestamp('payed_2_at')->nullable();
            $table->integer('payed_2')->unsigned()->nullable();
            $table->timestamp('payed_3_at')->nullable();
            $table->integer('payed_3')->unsigned()->nullable();
            $table->timestamp('payed_4_at')->nullable();
            $table->integer('payed_4')->unsigned()->nullable();
            $table->timestamp('payed_5_at')->nullable();
            $table->integer('payed_5')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('invited_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('inviter_id')->references('id')->on('users')->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refs');
    }
}
