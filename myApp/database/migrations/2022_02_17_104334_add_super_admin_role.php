<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSuperAdminRole extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('roles')->insert([
            'id'          => 3,
            'name'        => 'sa',
            'description' => 'Super Admin',
        ]);

        DB::table('role_user')
        ->where('user_id', DB::table('users')->where('id', 1)->first()->id)
        ->update([
            'role_id' => 3
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('roles')->where('id', 3)->delete();
        DB::table('role_user')
        ->where('user_id', DB::table('users')->where('id', 1)->first()->id)
        ->update([
            'role_id' => DB::table('roles')->where('name', 'admin')->first()->id
        ]);
    }
}
