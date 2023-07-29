<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'paypal_email' => $this->faker->unique()->safeEmail(),
            'username' => $this->faker->userName(),
            'name' => $this->faker->name(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'dob' => $this->faker->date(),
            'password' => bcrypt('Test@12345'),
            'is_activated' => true,
        ];
    }

    public function emptyEmail()
    {
        return $this->state(function () {
            return [
                'email' => ''
            ];
        });
    }

    public function invalidEmail()
    {
        return $this->state(function () {
            return [
                'email' => 'invalid-email#gmail'
            ];
        });
    }

    public function duplicateEmail()
    {
        return $this->state(function () {
            return [
                'email' => 'duplicate@duplicate.com'
            ];
        });
    }

    public function emptyPassword()
    {
        return $this->state(function () {
            return [
                'password' => ''
            ];
        });
    }

    public function invalidPassword()
    {
        return $this->state(function () {
            return [
                'password' => 'test123'
            ];
        });
    }

    public function registerState()
    {
        return $this->state(function () {
            return [
                'password' => 'FakePass@08',
                'activation_code' => '',
            ];
        });
    }

    public function nonActivated()
    {
        return $this->state(function () {
            return [
                'username' => null,
                'paypal_email' => null,
                'is_activated' => false,
                'activation_code' => 'ABCD-123',
            ];
        });
    }


    public function nonActivatedExternalId()
    {
        return $this->state(function () {
            return [
                'username' => null,
                'paypal_email' => null,
                'is_activated' => false,
                'external_id' => 'ABCD-123',
            ];
        });
    }
}
