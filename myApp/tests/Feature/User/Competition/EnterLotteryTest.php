<?php

namespace Tests\Feature\User\Competition;

use App\Models\Competition;
use App\Models\Group;
use Tests\TestCase;

class EnterLotteryTest extends TestCase
{
    private $url = '/api/user/lotteries/enter';
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserAndLogin();
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testEnterLotteryFailsWithInvalidId($id)
    {
        Competition::factory()->started()->lottery()->create()->id;

        $response = $this->postJson($this->url, ['id' => $id]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('competition_participants', 0);
    }

    public function testUserCannotEnterLotteryTwice()
    {
        $competitionId = Competition::factory()->started()->lottery()->create()->id;
        $this->assertDatabaseCount('competition_participants', 0);

        $response = $this->postJson($this->url, ['id' => $competitionId]);
        $response->assertOk();
        $this->assertDatabaseCount('competition_participants', 1);

        $response = $this->postJson($this->url, ['id' => $competitionId]);
        $response->assertUnprocessable();
        $this->assertDatabaseCount('competition_participants', 1);
    }

    public function testUserCannotEnterLotteryWhenAGroupIsAssignedAndTheyAreNotInIt()
    {
        $competitionId = Competition::factory()
                ->started()
                ->lottery()
                ->hasGroups(1)
                ->create()
                ->id;

        $response = $this->postJson($this->url, ['id' => $competitionId]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('competition_participants', 0);
    }

    /**
     * @dataProvider nonLiveLotteryProvider
     */
    public function testUserCannotEnterNonLiveLottery(array $competitionData)
    {
        $competitionId = Competition::factory()->create($competitionData)->id;

        $response = $this->postJson($this->url, ['id' => $competitionId]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('competition_participants', 0);
    }

    public function nonLiveLotteryProvider(): array
    {
        return [
            'Pending competition' => [
                ['state' => 'pending']
            ],
            'Ended competition' => [
                ['state' => 'ended']
            ],
            'Draft competition' => [
                ['status' => 'draft']
            ],
        ];
    }

    public function testUserCanEnterLiveLottery()
    {
        $competitionId = Competition::factory()->started()->lottery()->create()->id;

        $response = $this->postJson($this->url, ['id' => $competitionId]);

        $response->assertOk();
        $this->assertDatabaseCount('competition_participants', 1);
        $this->assertDatabaseHas('competition_participants', [
            'competition_id' => $competitionId,
            'user_id' => $this->user->id,
        ]);
    }

    public function testUserCanEnterLiveLotteryWhenAGroupIsAssignedAndTheyAreInIt()
    {
        $competitionId = Competition::factory()
                ->started()
                ->lottery()
                ->has(Group::factory()->hasAttached($this->user), 'groups')
                ->create()
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