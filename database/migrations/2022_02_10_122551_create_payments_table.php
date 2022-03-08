<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 15)->index();
            $table->string('token_id', 50)->nullable();
            $table->smallInteger('province_id')->unsigned();
            $table->integer('amount')->unsigned()->nullable();
            $table->string('card_holder', 20)->nullable();
            $table->string('Shaparak_Ref_Id', 30)->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('pay_for', 10)->nullable();
            $table->bigInteger('pay_for_id')->unsigned()->nullable();
            $table->bigInteger('coupon_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('no action');
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
        Schema::dropIfExists('payments');
    }
}
