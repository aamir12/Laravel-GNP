<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DeliverableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */    
    public function run()
    {
        factory(App\Models\Deliverable::class)->create();
    }
} 
