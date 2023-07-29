<?php
namespace Database\Factories;

use App\Models\Competition;
use App\Models\CompetitionParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetitionParticipantFactory extends Factory
{
 /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompetitionParticipant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'competition_id' => Competition::factory()
        ];
    }
}