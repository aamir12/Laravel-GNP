<?php

namespace Tests\Feature;

use App\Models\Competition;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CompetitionCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed --class=CompetitionSeeder');
    }

    public function testStartCommandStartsCorrectCompetitions()
    {
        $startedCompetition = Competition::whereDate('start_date', '=', date('Y-m-d'))->get();
        $futureStartCompetition = Competition::whereDate('start_date', '>', date('Y-m-d'))->get();
        $pastEndCompetition = Competition::whereDate('end_date', '<=', date('Y-m-d'))->where(['state' => 'ended'])->get();

        Artisan::call('competitions:start');

        $this->assertEquals($futureStartCompetition[0]->state, 'pending');
        $this->assertDatabaseHas('competitions', [
            'id' => $futureStartCompetition[0]->id,
            'state' => 'pending'
        ]);

        $this->assertEquals($startedCompetition[0]->state, 'pending');
        $this->assertDatabaseHas('competitions', [
            'id' => $startedCompetition[0]->id,
            'state' => 'started'
        ]);

        $this->assertEquals($pastEndCompetition[0]->state, 'ended');
        $this->assertDatabaseHas('competitions', [
            'id' => $pastEndCompetition[0]->id,
            'state' => 'ended'
        ]);
    }

    public function testStartCommandAutoEntersUsersIntoCompetitions()
    {
        $startedCompetition = Competition::whereDate('start_date', '=', date('Y-m-d'))->where(['auto_enter_user' => 1])->get();

        Artisan::call('competitions:start');

        $this->assertEquals($startedCompetition[0]->state, 'pending');
        $this->assertDatabaseHas('competitions', [
            'id' => $startedCompetition[0]->id,
            'state' => 'started'
        ]);
        $this->assertDatabaseHas('competition_participants', [
            'competition_id' => $startedCompetition[0]->id
        ]);
    }

    public function testEndCommandEndsCorrectCompetitions()
    {
        $futureStartCompetition = Competition::whereDate('start_date', '>', date('Y-m-d'))->get();
        $endCompetition = Competition::whereDate('end_date', '=', date('Y-m-d'))->get();
        $futureEndCompetition = Competition::where(['state' => 'started'])->whereDate('end_date', '>', date('Y-m-d'))->get();

        Artisan::call('competitions:end');

        $this->assertEquals($futureStartCompetition[0]->state, 'pending');
        $this->assertDatabaseHas('competitions', [
            'id' => $futureStartCompetition[0]->id,
            'state' => 'pending'
        ]);

        $this->assertEquals($futureEndCompetition[0]->state, 'started');
        $this->assertDatabaseHas('competitions', [
            'id' => $futureEndCompetition[0]->id,
            'state' => 'started'
        ]);

        $this->assertEquals($endCompetition[0]->state, 'started');
        $this->assertDatabaseHas('competitions', [
            'id' => $endCompetition[0]->id,
            'state' => 'ended'
        ]);
    }
}
