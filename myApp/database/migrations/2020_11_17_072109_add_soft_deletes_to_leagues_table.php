<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletesToLeaguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('leagues') && Schema::hasColumn('leagues', 'isDeleted')) {
            Schema::table('leagues', function (Blueprint $table) {
                $table->dropColumn('isDeleted');
            });
        }
        Schema::table('leagues', function (Blueprint $table) {
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
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->boolean('isDeleted',20)->default(0)->comment('1 Delete 0 NotDeleted');
        });
    }
}
