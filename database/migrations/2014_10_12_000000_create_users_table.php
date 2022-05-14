<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('family', 50)->nullable();
            $table->string('username', 50)->nullable();
            $table->string('email', 50)->unique()->nullable();
            $table->boolean('email_verified')->default(false);
            $table->string('phone', 30)->unique();
            $table->boolean('phone_verified')->default(false);
            $table->string('password')->nullable();
            $table->integer('score')->default(0);
            $table->string('role', 2)->default("us");
            $table->boolean('active')->default(false);
            $table->string('sheba', 24)->default(null)->nullable();
            $table->string('cart', 16)->default(null)->nullable();
            $table->string('ref_code', 10);
            $table->rememberToken();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->timestamp('expires_at')->nullable()->default(null);
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
