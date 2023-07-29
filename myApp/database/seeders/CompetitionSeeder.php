<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Group;
use App\Models\Prize;
use App\Models\Score;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompetitionSeeder extends Seeder
{
    public function run()
    {
        $this->createTodayStartDateCompetition();
        $this->createAutoEnterCompetition();
        $this->createFutureDateCompetition();
        $this->createTodayEndDateCompetition();
        $this->createPastEndDateCompetition();
        $this->createFutureEndDateCompetition();
    }

    public function createFutureDateCompetition () {
        return Competition::factory(['start_date' => date('Y-m-d H:i', strtotime('+1 days')), 'end_date' => date('Y-m-d H:i', strtotime('+7 days'))])->create();
    }

    public function createFutureEndDateCompetition () {
        return Competition::factory(['start_date' => date('Y-m-d H:i', strtotime('-2 days')), 'state' => 'started', 'end_date' => date('Y-m-d H:i', strtotime('+2 days'))])->create();
    }

    public function createTodayStartDateCompetition () {
        return Competition::factory(['start_date' => date('Y-m-d H:i'), 'end_date' => date('Y-m-d H:i', strtotime('+7 days'))])->create();
    }

    public function createAutoEnterCompetition () {
        $competition = Competition::factory(['start_date' => date('Y-m-d H:i'), 'end_date' => date('Y-m-d H:i', strtotime('+7 days')), 'auto_enter_user' => 1])->count(3)->create();
        $this->createAutoEnterUser($competition[0]);
        $this->createEnterGroupUser($competition[1]);
        $this->createEnterSubGroupUser($competition[2]);
    }

    public function createTodayEndDateCompetition () {
        $compinfo = Competition::factory([
            'start_date' => date('Y-m-d H:i', strtotime('-2 days')),
            'end_date' => date('Y-m-d H:i'),
            'status' => 'live',
            'state' => 'started',
            'auto_enter_user' => 1
        ])->create();
        $stock = Stock::factory()->create(['image' => 'test.jpg']);
        $prize = Prize::factory()->create(['stock_id' => $stock->id, 'competition_id' => $compinfo->id, 'max_winners' => 2]);
        $users = User::factory()->count(3)->create();
        $score = Score::factory()->create(['user_id' => $users[0]->id, 'timestamp' => date('Y-m-d H:i', strtotime('-1 days'))]);
        $score = Score::factory()->create(['user_id' => $users[1]->id, 'timestamp' => date('Y-m-d H:i', strtotime('-1 days'))]);
        $score = Score::factory()->create(['user_id' => $users[2]->id, 'timestamp' => date('Y-m-d H:i', strtotime('-1 days'))]);
        $compinfo->entrants()->attach([$users[0]->id, $users[1]->id, $users[2]->id]);
    }

    public function createAutoEnterUser($comp) {
        $normalUser = User::factory()->create();
        $comp->entrants()->attach([$normalUser->id]);
    }

    public function createEnterGroupUser($comp) {
        $group = Group::factory()->default()->create();
        $commonuser = User::factory()->create();
        $group->users()->attach($commonuser->id);
        $comp->groups()->attach([$group->id]);
        $comp->entrants()->attach([$commonuser->id]);
    }

    public function createEnterSubGroupUser($comp) {
        $group = Group::factory()->default()->create();
        $subgroup1 = Group::factory()->create(['parent_id' => $group->id, 'is_default_group' => 0]);
        $subgroup2 = Group::factory()->create(['parent_id' => $group->id, 'is_default_group' => 0]);
        $users = User::factory()->count(3)->create();
        $subgroup1->users()->attach($users[1]->id);
        $subgroup1->users()->attach($users[0]->id);
        $subgroup2->users()->attach($users[2]->id);
        $subgroup2->users()->attach($users[0]->id);
        $comp->groups()->attach([$group->id]);
        $comp->entrants()->attach([$users[0]->id, $users[1]->id, $users[2]->id]);
    }

    public function createPastEndDateCompetition () {
        return Competition::factory([
            'start_date' => date('Y-m-d H:i', strtotime('-3 days')),
            'end_date' => date('Y-m-d H:i', strtotime('-1 days')),
            'state' => 'ended',
            'status' => 'archived'
        ])->create();
    }
}
