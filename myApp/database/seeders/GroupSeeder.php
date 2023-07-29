<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */    
    public function run()
    {
        factory(App\Models\Group::class)->create();
    }
} 
