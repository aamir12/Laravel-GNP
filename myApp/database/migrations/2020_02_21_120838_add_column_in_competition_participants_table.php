<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInCompetitionParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competition_participants', function (Blueprint $table) {
            $table->boolean('competition_revealed',1)->default(0)->comment('1 Yes 0 No')->after('competition_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competition_participants', function (Blueprint $table) {
            $table->dropColumn('competition_revealed');
        });
    }
}
