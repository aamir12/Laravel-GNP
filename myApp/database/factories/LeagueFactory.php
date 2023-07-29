<?php

namespace Database\Factories;

use App\Models\League;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeagueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = League::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
		    'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            // 'image' => 'test.png',//$faker->image('storage/app',640,480, null, false),
            // 'parent_id' => 0,
            'owner_id' => User::factory(),
            'score_aggregation_period' => ['daily', 'weekly', 'monthly'][array_rand(['daily', 'weekly', 'monthly'])],
            'type' => ['Public', 'Private'][array_rand(['Public', 'Private'])],
            // 'group_id' => 0
        ];
    }
}