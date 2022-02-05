<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->smallInteger('category_id')->unsigned();
            $table->string('title', 200);
            $table->string('summary', 500);
            $table->json('content');
            $table->tinyText('tags')->nullable();//0-255 character
            $table->boolean('is_draft')->default(false);
            $table->timestamp('published_at')->useCurrent();
            $table->boolean('active')->default(true);
            $table->timestamps();


            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blogs');
    }
}
