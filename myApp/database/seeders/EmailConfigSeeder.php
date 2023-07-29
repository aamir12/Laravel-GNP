<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmailConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\EmailConfig::class)->create();
    }
}
