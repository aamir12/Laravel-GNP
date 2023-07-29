<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeagueInviteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('league_invites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('league_owner_id')->unsigned()->index();
            $table->bigInteger('invitee_id')->unsigned()->index();
            $table->bigInteger('league_id')->unsigned()->index();
            $table->boolean('accepted')->default(0)->comment('1 Accepted 0 NotAccepted');
            $table->boolean('rejected')->default(0)->comment('1 Rejected 0 NotRejected');
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
        Schema::dropIfExists('league_invites');
    }
}