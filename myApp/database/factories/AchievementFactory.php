<?php
namespace Database\Factories;

use App\Models\Achievement;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

class AchievementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Achievement::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'stock_id' => Stock::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'image' => 'test.png'
        ];
    }

    public function withoutStockId()
    {
        return $this->state(function () {
            return [
                'stock_id' => null
            ];
        });
    }

    public function withoutImage()
    {
        return $this->state(function () {
            return [
                'image' => null
            ];
        });
    }
}
