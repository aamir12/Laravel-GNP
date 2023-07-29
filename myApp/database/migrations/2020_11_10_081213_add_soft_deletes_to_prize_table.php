<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletesToPrizeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('prizes') && Schema::hasColumn('prizes', 'is_deleted')) {
            Schema::table('prizes', function (Blueprint $table) {
                $table->dropColumn('is_deleted');
            });
        }
        Schema::table('prizes', function (Blueprint $table) {
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
        Schema::table('prizes', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->boolean('is_deleted',20)->default(0)->comment('1 Delete 0 NotDeleted');
        });
    }
}
