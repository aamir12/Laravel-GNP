<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CompetitionProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $group = factory(App\Models\Group::class)->create();
        
        $competition = factory(App\Models\Competition::class)->create([ 'group' => $group->id ]);
        
        factory(App\Models\User::class, 2)->create()->each(function ($user) use($group) {
                \App\Models\User::find($user->id)->group()->save(\App\Models\Group::find($group->id));
        });
    }
}
