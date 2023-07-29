<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertCompetitionEventEmailsInEmailConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('email_configs')->insert([
            [
                'email_type' => 'competition_started',
                'is_enabled' => true,
                'subject' => 'Competition Started!',
                'body' => '<p>A competition you could enter has started!</p>',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'email_type' => 'competition_ended',
                'is_enabled' => true,
                'subject' => 'Competition Ended!',
                'body' => '<p>A competition you entered has ended!</p>',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('email_configs')
            ->whereIn('email_type', ['competition_started', 'competition_ended'])
            ->delete();
    }
}
