<?php

namespace Tests\Feature\User\Competition;

use App\Models\Competition;
use App\Models\Group;
use Tests\TestCase;

class EnterCompetitionTest extends TestCase
{
    private $url = '/api/user/competitions/enter';
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserAndLogin();
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testEnterCompetitionFailsWithInvalidId($id)
    {
        Competition::factory()->started()->create(['is_lottery' => 0])->id;

        $response = $this->postJson($this->url, ['id' => $id]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('competition_participants', 0);
    }

    public function testUserCannotEnterCompetitionTheyHaveAlreadyEntered()
    {
        $competitionId = Competition::factory()
            ->hasAttached($this->user, [], 'entrants')
            ->started()
            ->create(['is_lottery' => 0])
            ->id;
        $this->assertDatabaseCount('competition_participants', 1);

        $response = $this->postJson($this->url, ['id' => $competitionId]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('competition_participants', 1);
    }

    public function testUserCannotEnterCompetitionWhenAGroupIsAssignedAndTheyAreNotInIt()
    {
        $competitionId = Competition::factory()
                ->started()
                ->hasGroups(1)
                ->create(['is_lottery' => 0])
                ->id;

        $response = $this->postJson($this->url, ['id' => $competitionId]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('competition_participants', 0);
    }

    /**
     * @dataProvider nonLiveCompetitionProvider
     */
    public function testUserCannotEnterNonLiveCompetition(array $competitionData)
    {
        $competitionData['is_lottery'] = 0;
        $competitionId = Competition::factory()->create($competitionData)->id;

        $response = $this->postJson($this->url, ['id' => $competitionId]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('competition_participants', 0);
    }

    public function nonLiveCompetitionProvider(): array
    {
        return [
            'Pending competition' => [ ['state' => 'pending'] ],
            'Ended competition' => [ ['state' => 'ended'] ],
            'Draft competition' => [ ['status' => 'draft'] ],
        ];
    }

    public function testUserCanEnterLiveCompetition()
    {
        $competitionId = Competition::factory()->started()->create(['is_lottery' => 0])->id;

        $response = $this->postJson($this->url, ['id' => $competitionId]);

        $response->assertOk();
        $this->assertDatabaseCount('competition_participants', 1);
        $this->assertDatabaseHas('competition_participants', [
            'competition_id' => $competitionId,
            'user_id' => $this->user->id,
        ]);
    }

    public function testUserCanEnterLiveCompetitionWhenAGroupIsAssignedAndTheyAreInIt()
    {
        $competitionId = Competition::factory()
                ->started()
                ->has(Group::factory()->hasAttached($this->user), 'groups')
                ->create(['is_lottery' => 0])
                ->id;

        $response = $this->postJson($this->url, ['id' => $competitionId]);

        $response->assertOk();
        $this->assertDatabaseCount('competition_participants', 1);
        $this->assertDatabaseHas('competition_participants', [
            'competition_id' => $competitionId,
            'user_id' => $this->user->id,
        ]);
    }
}