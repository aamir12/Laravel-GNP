<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertInEmailConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('email_configs')->insert([
            'email_verification' => 1, 
            'invitation' => 1, 
            'invitation_reminder' => 1, 
            'account_changes' => 1, 
            'password_reset' => 1, 
            'competition_result' => 1, 
            'league_invites' => 1, 
            'invitation_reminder_time_delay' => 7,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::table('email_configs')->delete();
    }
}
