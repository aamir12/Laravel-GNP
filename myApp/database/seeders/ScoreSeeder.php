<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Score::class)->create();
    }
}
