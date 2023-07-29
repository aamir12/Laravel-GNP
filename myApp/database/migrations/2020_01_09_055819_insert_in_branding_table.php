<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertInBrandingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('brandings')->insert([
            'logo' => null, 
            'primary_color' => '#0052cc', 
            'company_address' => 1, 
            'terms_url' => 'www.abcx.com', 
            'privacy_url' => 'www.abcx.com', 
            'support_email' => 'support@gip.com', 
            'image_type' => 'local',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::table('brandings')->delete();
    }
}
