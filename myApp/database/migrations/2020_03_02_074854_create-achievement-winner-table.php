<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAchievementWinnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('achievement_winners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('achievement_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('deliverable_id')->nullable();
            $table->boolean('is_claimed',1)->default(0)->comment('1 Claimed 0 Not Claimed');
            $table->boolean('isDeleted',1)->default(0)->comment('1 Delete 0 NotDeleted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('achievement_winners');
    }
}
