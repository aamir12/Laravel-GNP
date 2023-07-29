<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertInRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('roles')->insert([
            'id'  => 1,
            'name' => 'admin',
            'description'    => 'Admin',
        ]);
        DB::table('roles')->insert([
            'id'  => 2,
            'name' => 'user',
            'description'    => 'User',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('roles')->where('id', 1)->orWhere('id', 2)->delete();
    }
}
