<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTimestampFieldDataTypeInScoresAndScoreArchivesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->timestamp('timestamp', 3)->change();
        });

        Schema::table('score_archives', function (Blueprint $table) {
            $table->timestamp('timestamp', 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dateTime('timestamp')->change();
        });

        Schema::table('score_archives', function (Blueprint $table) {
            $table->dateTime('timestamp')->change();
        });
    }
}
