<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAdminUserRoleIfAbsent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $adminId = DB::table('users')->where('email', 'admin@gip.com')->first()->id;
        $adminRoleExists = DB::table('role_user')->where('user_id', $adminId)->exists();

        if (!$adminRoleExists) {
            DB::table('role_user')->insert([
                'role_id' => DB::table('roles')->where('name', 'admin')->first()->id,
                'user_id' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $admin = DB::table('users')->where('email', 'admin@gip.com')->first();

        if ($admin) {
            DB::table('role_user')->where('user_id', $admin->id)->delete();
        }
    }
}
