<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Competition;
use App\Models\Stock;
use App\Models\Prize;
use App\Models\Score;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    private $userId;
    private $email = 'test@test.com';
    private $username = 'test';
    private $password = 'Test@12345';
    private $competitionId;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->createUsers();
        $this->setUpTestUser();
        $this->createCompetitionsForUser();
        $this->createPrize();
        $this->createScore();
    }

    private function createUsers()
    {
        $roleId = DB::table('roles')->firstWhere(['name' => 'user'])->id;
        User::factory()->count(10)->create()->each(function ($user) use ($roleId) {
            $user->roles()->attach($roleId);
        });
    }

    private function setUpTestUser()
    {
        $user = User::firstWhere('email', $this->email);
        if (!$user) {
            $roleId = DB::table('roles')->firstWhere(['name' => 'user'])->id;
            User::factory()
                ->create([
                    'email' => $this->email,
                    'username' => $this->username,
                    'password' => $this->password
                ])
                ->each(function ($user) use ($roleId) {
                $user->roles()->attach($roleId);
            });
        } else {
            $user->password = bcrypt($this->password);
            $user->save();
        }
    }

    private function createCompetitionsForUser()
    {
        $this->userId = DB::table('users')->orderby('created_at', 'desc')->first()->id;

        $competition = Competition::factory()->started()->create();
        $competition->entrants()->attach($this->userId);

        $competition = Competition::factory()->upcoming()->create();
        $competition->entrants()->attach($this->userId);

        $competition = Competition::factory()->ended()->create();
        $competition->entrants()->attach($this->userId);
    }

    private function createPrize()
    {
        $this->competitionId =  DB::table('competitions')->orderby('created_at', 'desc')->take(3)->get();
        foreach($this->competitionId as $comp) {
            Stock::factory()
            ->hasPrize(1,['competition_id' => $comp->id, 'max_winners' => 1])
            ->create();
        }
    }

    private function createScore()
    {
        $this->competitionId =  DB::table('competitions')->orderby('created_at', 'desc')->take(3)->get();
        foreach($this->competitionId as $comp) {
            dump($comp);
            $int= rand(strtotime($comp->start_date),strtotime($comp->end_date));
            Score::factory()->create(['user_id' => $this->userId, 'value' => rand(100,150), 'timestamp' => date('Y-m-d H:i', $int) ]);
        }
    }
}
