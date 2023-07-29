<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DeleteGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = factory(App\Models\User::class)->create();        
        $group = factory(App\Models\Group::class)->create();
        $group2 = factory(App\Models\Group::class)->states('delete')->create();
        $user->groups()->save($group); 
        // return [
        //     'user' => $user,
        //     'group' => $group
        // ];
    }
}
