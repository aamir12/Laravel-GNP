<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterGroupInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        Schema::rename('group_infos', 'group_user');
        Schema::table('group_user', function (Blueprint $table) {
            $table->renameColumn('group_info_id', 'user_id');
            $table->dropColumn('group_info_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        Schema::rename('group_user', 'group_infos');
        Schema::table('group_infos', function (Blueprint $table) {
            $table->renameColumn('user_id','group_info_id');
            $table->string('group_info_type');
        });
    }
}
