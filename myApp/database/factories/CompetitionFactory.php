<?php

namespace Database\Factories;

use App\Models\Competition;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetitionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Competition::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $tomorrow = Carbon::tomorrow()->getTimestamp();
        $endDate = Carbon::tomorrow()->addMonth()->getTimestamp();
        //Custom range.
        $timestamp = mt_rand($tomorrow, $endDate);
        $date = date('Y-m-d H:i', $timestamp);
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'image' => 'test.png',//$this->faker->image('storage/app',640,480, null, false),
            'type' => 'Fixed',
            'score_threshold' => '100',
            'period' => 'daily',
            'start_date' => $date,
            // 'end_date' => $date->addDays(1)->format('Y-m-d H:i:s'),
            'is_lottery' => 1,
            'space_count' => 0,
            'entry_fee' => 0,
            'terms_url' => $this->faker->url(),
            'auto_enter_user' => 0,
            'status' => 'live',
            'state' => 'pending',
        ];
    }

    public function started()
    {
        return $this->state(function (array $attributes) {
            return [
                'start_date' => date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d')))) . ' 00:00',
                'end_date' =>  date('Y-m-d', strtotime('+2 days', strtotime(date('Y-m-d')))) . ' 00:00',
                'status' => 'live',
                'state' => 'started'
            ];
        });
    }

    public function upcoming()
    {
        return $this->state(function (array $attributes) {
            return [
                'start_date' => date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d')))) . ' 00:00',
                'end_date' =>  date('Y-m-d', strtotime('+30 days', strtotime(date('Y-m-d')))) . ' 00:00',
                'status' => 'live',
                'state' => 'pending',
            ];
        });
    }

    public function ended()
    {
        return $this->state(function (array $attributes) {
            return [
                'start_date' => date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d')))) . ' 00:00',
                'end_date' =>  date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d')))) . ' 00:00',
                'status' => 'archived',
                'state' => 'ended'
            ];
        });
    }

    public function live()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_lottery' => false,
                'status' => 'live'
            ];
        });
    }

    public function lottery() {
        return $this->state(fn() => ['is_lottery' => true]);
    }

    public function autoEnter()
    {
        return $this->state(fn() => ['auto_enter_user' => true]);
    }
}
