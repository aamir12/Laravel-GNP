<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLoginActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('login_activities', function ($table) {            
            $table->dropForeign(['user_id'])->nullable(false)->change(); //Droping foreign key
        });
        Schema::table('login_activities', function ($table) {
            $table->unsignedBigInteger('user_id')->length(20)->nullable(false)->change();//changing length
        });
        Schema::table('users', function ($table) {
            $table->bigIncrements('id')->length(20)->change();
        });
        Schema::table('login_activities', function ($table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('login_activities', function ($table) {            
            $table->dropForeign(['user_id'])->nullable(false)->change(); //Droping foreign key
        });
        Schema::table('login_activities', function ($table) {
            $table->unsignedInteger('user_id')->length(10)->nullable(false)->change();//changing length
        });
        Schema::table('users', function ($table) {
            $table->increments('id')->length(10)->change();
        });
        Schema::table('login_activities', function ($table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
