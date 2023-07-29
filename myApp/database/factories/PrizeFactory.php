<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Prize;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrizeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Prize::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'stock_id' => Stock::factory(),
            'competition_id' => Competition::factory(),
            'reference' => $this->faker->word(),
            'max_winners' => rand(0, 10),
        ];
    }
}

