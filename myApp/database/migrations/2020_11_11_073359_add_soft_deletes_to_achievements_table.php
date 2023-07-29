<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletesToAchievementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('achievements') && Schema::hasColumn('achievements', 'isDeleted')) {
            Schema::table('achievements', function (Blueprint $table) {
                $table->dropColumn('isDeleted');
            });
        }
        Schema::table('achievements', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->boolean('isDeleted',20)->default(0)->comment('1 Delete 0 NotDeleted');
        });
    }
}
