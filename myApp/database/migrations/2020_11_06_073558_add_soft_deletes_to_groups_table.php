<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletesToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('groups') && Schema::hasColumn('groups', 'is_deleted')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropColumn('is_deleted');
            });
        }
        Schema::table('groups', function (Blueprint $table) {
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
        Schema::table('groups', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->boolean('is_deleted',20)->default(0)->comment('1 Delete 0 NotDeleted');
        });
    }
}
