<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLongtextColumnTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->text('description')->change();
        });
        Schema::table('achievements', function (Blueprint $table) {
            $table->text('description')->change();
        });
        Schema::table('leagues', function (Blueprint $table) {
            $table->text('description')->change();
        });
        Schema::table('brandings', function (Blueprint $table) {
            $table->string('company_address')->change();
        });
        Schema::table('deliverables', function (Blueprint $table) {
            $table->string('shipping_comment')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->longText('description')->change();
        });
        Schema::table('achievements', function (Blueprint $table) {
            $table->longText('description')->change();
        });
        Schema::table('leagues', function (Blueprint $table) {
            $table->longText('description')->change();
        });
        Schema::table('brandings', function (Blueprint $table) {
            $table->longText('company_address')->change();
        });
        Schema::table('deliverables', function (Blueprint $table) {
            $table->longText('shipping_comment')->change();
        });
    }
}
