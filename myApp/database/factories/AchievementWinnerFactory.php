<?php

namespace Database\Factories;

use App\Models\Achievement;
use App\Models\AchievementWinner;
use App\Models\Deliverable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AchievementWinnerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AchievementWinner::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'achievement_id' => Achievement::factory(),
            'user_id' => User::factory(),
            'deliverable_id' => Deliverable::factory(),
            'is_claimed' => false,
        ];
    }

    public function claimed()
    {
        return $this->state(function () {
            return [
                'is_claimed' => true
            ];
        });
    }
}


