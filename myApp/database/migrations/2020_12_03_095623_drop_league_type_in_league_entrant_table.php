<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropLeagueTypeInLeagueEntrantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('league_entrants') && Schema::hasColumn('league_entrants', 'league_type')) {
            Schema::table('league_entrants', function (Blueprint $table) {
                $table->dropColumn('league_type');
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
        Schema::table('league_entrants', function (Blueprint $table) {
            $table->string('league_type');
        });
    }
}
