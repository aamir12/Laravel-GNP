<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LeagueInviteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\LeagueInvite::class)->create();
    }
}
