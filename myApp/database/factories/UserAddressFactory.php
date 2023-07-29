<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name(),
            'is_default' => true,
            'address_line_1' => $this->faker->address(),
            'town' => $this->faker->city(),
            'postcode' => $this->faker->numerify('#####'),
            'country' => $this->faker->country(),
        ];
    }
}
