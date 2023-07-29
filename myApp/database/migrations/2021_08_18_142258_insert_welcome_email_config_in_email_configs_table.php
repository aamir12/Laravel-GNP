<?php

use Illuminate\Database\Migrations\Migration;

class InsertWelcomeEmailConfigInEmailConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('email_configs')->insert([
            'email_type' => 'welcome',
            'subject' => 'Welcome to Earnie!',
            'body' => '<p>You\'re in!</p>',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('email_configs')->where('email_type', 'welcome')->delete();
    }
}
