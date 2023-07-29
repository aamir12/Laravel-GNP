<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmin extends Migration
{
    public function up()
    {
        DB::table('users')->insert([
            'first_name' => 'GIP',
            'last_name' => 'Admin',
            'name' => 'GIP Admin',
            'username' => 'admin',
            'email' => 'admin@gip.com',
            'password' => bcrypt('123'),
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        DB::table('role_user')->insert([
            'role_id' => DB::table('roles')->where('name', 'admin')->first()->id,
            'user_id' => DB::table('users')->where('email', 'admin@gip.com')->first()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $adminId = DB::table('users')->where('email', 'admin@gip.com')->delete();
        DB::table('role_user')->where('user_id', $adminId)->delete();
    }
}
