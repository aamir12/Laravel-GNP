<?php
namespace Database\Factories;

use App\Models\League;
use App\Models\LeagueInvite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeagueInviteFactory extends Factory
{
 /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LeagueInvite::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'league_owner_id' => User::factory(),
            'invitee_id' => User::factory(),
            'league_id' => League::factory(),
            'accepted' => false,
            'rejected' => false,
        ];
    }

    public function accepted()
    {
        return $this->state(fn() => ['accepted' => true]);
    }

    public function rejected()
    {
        return $this->state(fn() => ['rejected' => true]);
    }
}