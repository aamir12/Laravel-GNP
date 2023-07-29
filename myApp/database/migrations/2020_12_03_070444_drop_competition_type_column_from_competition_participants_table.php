<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropCompetitionTypeColumnFromCompetitionParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('competition_participants') && Schema::hasColumn('competition_participants', 'competition_type')) {
            Schema::table('competition_participants', function (Blueprint $table) {
                $table->dropColumn('competition_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competition_participants', function (Blueprint $table) {
            $table->string('competition_type');
        });
    }
}
