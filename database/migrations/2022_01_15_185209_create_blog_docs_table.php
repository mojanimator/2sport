<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog-docs', function (Blueprint $table) {
            $table->id();
//            $table->string('path', 100);
            $table->tinyInteger('type_id')->unsigned();
            $table->timestamp('created_at')->useCurrent();

            $table->bigInteger('docable_id')->unsigned()->index();

            $table->foreign('type_id')->references('id')->on('doc-types')->onDelete('no action');
//            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog-docs');
    }
}
