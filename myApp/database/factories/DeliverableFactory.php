<?php

namespace Database\Factories;

use App\Models\Deliverable;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliverableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Deliverable::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'is_shipped' => rand(0, 1),
            'shipping_name' => $this->faker->name(),
            'shipping_number' => $this->faker->randomNumber(8),
            'shipping_email' => $this->faker->unique()->safeEmail(),
            'shipping_addressline1' => $this->faker->streetAddress(),
            'shipping_addressline2' => $this->faker->streetAddress(),
            'shipping_addressline3' => $this->faker->streetAddress(),
            'shipping_postcode' => $this->faker->postcode(),
            'shipping_county' => $this->faker->state(),
            'shipping_country' => $this->faker->country(),
            'shipping_comment' => $this->faker->sentence(),
            'tracking_ref' => $this->faker->lexify('?????') //generate 5 characters random string
        ];
    }
}
