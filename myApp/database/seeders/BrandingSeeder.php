<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BrandingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Branding::class)->create();
    }
}
