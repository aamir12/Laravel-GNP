<?php
namespace Database\Factories;

use App\Models\Deliverable;
use App\Models\Prize;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Database\Eloquent\Factories\Factory;

class WinnerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Winner::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'prize_id' => Prize::factory(),
            'deliverable_id' => Deliverable::factory(),
            'is_claimed' => false,
            'is_revealed' => false,
        ];
    }

    public function claimed()
    {
        return $this->state(fn() => ['is_claimed' => true]);
    }
}

