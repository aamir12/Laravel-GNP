<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('email_verification')->default(0)->comment('1 true 0 false');
            $table->boolean('invitation')->default(0)->comment('1 true 0 false');
            $table->boolean('invitation_reminder')->default(0)->comment('1 true 0 false');
            $table->boolean('account_changes')->default(0)->comment('1 true 0 false');
            $table->boolean('password_reset')->default(0)->comment('1 true 0 false');
            $table->boolean('competition_result')->default(0)->comment('1 true 0 false');
            $table->boolean('league_invites')->default(0)->comment('1 true 0 false');
            $table->decimal('invitation_reminder_time_delay',10,2)->nullable();
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
        Schema::dropIfExists('email_configs');
    }
}
