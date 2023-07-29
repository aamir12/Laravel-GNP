<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpdatedByColumnOnAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable();
        });
        Schema::table('achievements', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable();
        });
        Schema::table('brandings', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable();
        });
        Schema::table('competitions', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable();
        });
        Schema::table('deliverables', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable();
        });
        Schema::table('email_configs', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable();
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable();
        });
        Schema::table('group_user', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable();
        });
        Schema::table('leagues', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable();
        });
        Schema::table('league_invites', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable();
        });
        Schema::table('prizes', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable();
        });
        Schema::table('stocks', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable();
        });
        Schema::table('scores', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
        Schema::table('brandings', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
        Schema::table('deliverables', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
        Schema::table('email_configs', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
        Schema::table('group_user', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
        Schema::table('league_invites', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
        Schema::table('prizes', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
    }
}
