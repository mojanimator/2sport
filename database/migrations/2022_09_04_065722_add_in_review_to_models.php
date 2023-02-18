<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInReviewToModels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('players', function (Blueprint $table) {
            $table->boolean('in_review')->default(false);

        });
        Schema::table('coaches', function (Blueprint $table) {
            $table->boolean('in_review')->default(false);

        });
        Schema::table('clubs', function (Blueprint $table) {
            $table->boolean('in_review')->default(false);

        });
        Schema::table('shops', function (Blueprint $table) {
            $table->boolean('in_review')->default(false);

        });
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('in_review')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn('in_review');
        });
        Schema::table('coaches', function (Blueprint $table) {
            $table->dropColumn('in_review');
        });
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn('in_review');
        });
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('in_review');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('in_review');
        });
    }
}
