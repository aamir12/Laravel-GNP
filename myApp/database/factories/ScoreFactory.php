<?php

namespace Database\Factories;

use App\Models\Score;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Score::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'value' => number_format(rand(0,100) / 7, 2, '.', ''),
            'weight' => rand(0,100) / 7,
            'timestamp' => $this->faker->date('Y-m-d H:i', '2019-07-20 13:50'),
        ];
    }

    public function allEmpty()
    {
        return $this->state(function () {
            return [
                'user_id' => '',
                'email' => '',
                'external_id' => ''
            ];
        });
    }

    public function withMetadata()
    {
        return $this->state(function () {
            return [
                'metadata' => '{"title": "john", "url": "https://temp-mail.org/en/"}'
            ];
        });
    }

    public function withEmail()
    {
        return $this->state(function () {
            return [
                'email' => 'Test@Kpi.com',
                'metadata' => '{"title": "john", "url": "https://temp-mail.org/en/"}'
            ];
        });
    }

    public function withExternalId()
    {
        return $this->state(function () {
            return [
                'external_id' => 'EXT123',
                'metadata' => '{"title":"john", "url": "https://temp-mail.org/en/"}'
            ];
        });
    }
}