<?php
namespace Database\Factories;

use App\Models\League;
use App\Models\LeagueEntrant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeagueEntrantFactory extends Factory
{
 /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LeagueEntrant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'league_id' => League::factory()
        ];
    }
}