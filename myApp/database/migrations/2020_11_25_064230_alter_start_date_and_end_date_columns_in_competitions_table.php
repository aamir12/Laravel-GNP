<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStartDateAndEndDateColumnsInCompetitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        Schema::table('competitions', function (Blueprint $table) {
            $table->datetime('start_date')->change();
            $table->datetime('end_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE competitions MODIFY start_date TIMESTAMP NULL');
        DB::statement('ALTER TABLE competitions MODIFY end_date TIMESTAMP NULL');
    }
}
