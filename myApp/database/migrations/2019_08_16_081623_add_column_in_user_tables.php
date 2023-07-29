<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInUserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username',100)->after('email')->nullable();
            $table->string('first_name',100)->after('username')->nullable();
            $table->string('last_name',100)->after('first_name')->nullable();
            $table->string('timezone',100)->nullable()->after('last_name');
            $table->string('phone',20)->nullable()->after('timezone');
            $table->date('dob')->nullable()->after('phone');
            $table->string('activation_code',255)->nullable()->after('dob');
            $table->string('external_id',255)->nullable()->after('activation_code');            
            $table->boolean('status',1)->default(0)->comment('1 Active 0 Inactive')->after('remember_token');
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
            $table->dropColumn('username');
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('timezone');
            $table->dropColumn('phone');
            $table->dropColumn('dob');
            $table->dropColumn('activation_code');
            $table->dropColumn('external_id');
            $table->dropColumn('status');
        });
    }
}
