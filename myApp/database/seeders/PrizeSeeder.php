<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PrizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Stock::class,1)->create()->each(function ($stock) {
            $stock->prize()->save(factory(App\Models\Prize::class)->make());
        });
    }
}
