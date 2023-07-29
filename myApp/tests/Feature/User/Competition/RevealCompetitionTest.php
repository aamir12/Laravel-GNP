<?php

namespace Tests\Feature\User\Competition;

use App\Models\Competition;
use App\Models\Prize;
use App\Models\Winner;
use Tests\TestCase;

class RevealCompetitionTest extends TestCase
{
    private $url = '/api/user/competitions/reveal';
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserAndLogin();
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testRevealCompetitionFailsWithInvalidId($id)
    {
        $competitionId = Competition::factory()
            ->hasAttached($this->user, [], 'entrants')
            ->ended()
            ->create(['is_lottery' => 0])
            ->id;

        $response = $this->postJson($this->url, ['id' => $id]);

        $response->assertUnprocessable();
        $this->assertDatabaseHas('competition_participants', [
            'competition_id' => $competitionId,
            'competition_revealed' => false,
        ]);
    }

    /**
     * @dataProvider notEndedCompetitionProvider
     */
    public function testUserCannotRevealACompetitionThatHasNotEnded(array $competitionData)
    {
        $competitionId = Competition::factory()
            ->hasAttached($this->user, [], 'entrants')
            ->state(['is_lottery' => 0])
            ->create($competitionData)
            ->id;

        $response = $this->postJson($this->url, ['id' => $competitionId]);

        $response->assertUnprocessable();
        $this->assertDatabaseHas('competition_participants', [
            'competition_id' => $competitionId,
            'competition_revealed' => false,
        ]);
    }

    public function notEndedCompetitionProvider(): array
    {
        return [
            'Pending competition' => [['state' => 'pending']],
            'Running competition' => [['state' => 'started']],
            'Draft competition' => [['status' => 'draft']],
        ];
    }

    public function testUserCannotRevealACompetitionThatTheyHaveNotEntered()
    {
        $competitionId = Competition::factory()
            ->ended()
            ->create(['is_lottery' => 0])
            ->id;

        $response = $this->postJson($this->url, ['id' => $competitionId]);
        $response->assertUnprocessable();
    }

    public function testUserCannotRevealACompetitionTheyHaveAlreadyRevealed()
    {
        $competitionId = Competition::factory()
            ->hasAttached($this->user, ['competition_revealed' => true], 'entrants')
            ->ended()
            ->create(['is_lottery' => 0])
            ->id;

        $response = $this->postJson($this->url, ['id' => $competitionId]);
        $response->assertUnprocessable();
        // Just to make sure that nothing has changed by mistake.
        $this->assertDatabaseHas('competition_participants', [
            'competition_id' => $competitionId,
            'competition_revealed' => true,
        ]);
    }

    public function testUserCanRevealACompetitionWhenTheyHaveNotWon()
    {
        $competitionId = Competition::factory()
            ->hasAttached($this->user, [], 'entrants')
            ->ended()
            ->create(['is_lottery' => 0])
            ->id;

        $response = $this->postJson($this->url, ['id' => $competitionId]);

        $response->assertOk();
        $response->assertJsonPath('data.is_winner', false);
        $response->assertJsonPath('data.prize', null);
        $this->assertDatabaseHas('competition_participants', [
            'competition_id' => $competitionId,
            'competition_revealed' => true,
        ]);
    }

    public function testUserCanRevealACompetitionWhenTheyHaveWon()
    {
        $competition = Competition::factory()
            ->hasAttached($this->user, [], 'entrants')
            ->ended()
            ->create(['is_lottery' => 0]);

        $prize = Prize::factory()
            ->for($competition)
            ->has(Winner::factory()->state(['user_id' => $this->user->id]))
            ->create();

        $response = $this->postJson($this->url, ['id' => $competition->id]);

        $response->assertOk();
        $response->assertJsonPath('data.is_winner', true);
        $response->assertJsonPath('data.prize.id', $prize->id);
        $response->assertJsonPath('data.prize.stock.id', $prize->stock->id);

        $this->assertDatabaseHas('competition_participants', [
            'competition_id' => $competition->id,
            'competition_revealed' => true,
        ]);
    }
}
