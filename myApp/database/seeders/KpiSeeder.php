<?php

namespace Database\Seeders;

use App\Models\Score;
use App\Models\User;
use Illuminate\Database\Seeder;

class KpiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(User $user)
    {
        Score::factory()->for($user)->count(2)->create([
            'timestamp' => now()->startOfDay(),
            'value' => 10,
            'weight' => 1,
        ]);

        Score::factory()->for($user)->create([
            'timestamp' => now()->startOfDay()->addHour(),
            'value' => 20,
            'weight' => 1.5,
        ]);

        Score::factory()->for($user)->count(3)->create([
            'timestamp' => now()->startOfWeek(),
            'value' => 10,
            'weight' => 1,
        ]);

        Score::factory()->for($user)->create([
            'timestamp' => now()->startOfWeek()->addHour(),
            'value' => 20,
            'weight' => 1.5,
        ]);

        Score::factory()->for($user)->count(4)->create([
            'timestamp' => now()->startOfMonth(),
            'value' => 10,
            'weight' => 1,
        ]);

        Score::factory()->for($user)->create([
            'timestamp' => now()->startOfMonth()->addHour(),
            'value' => 20,
            'weight' => 1.5,
        ]);
    }
}
