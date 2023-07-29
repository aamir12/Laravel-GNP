<?php

namespace Tests\Feature\User\Achievement;

use App\Models\AchievementWinner;
use Tests\TestCase;

class ClaimAchievementTest extends TestCase
{
    private $url = '/api/user/achievements/claim';
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserAndLogin();
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testAchievementFailsWithInvalidWinnerId($invalidId)
    {
        $id = AchievementWinner::factory()->for($this->user)->create()->id;

        $response = $this->postJson($this->url, ['achievement_winner_id' => $invalidId]);

        $response->assertUnprocessable();
        $this->assertDatabaseHas('achievement_winners', [
            'id' => $id,
            'is_claimed' => false
        ]);
    }

    public function testAchievementCannotBeClaimedMoreThanOnce()
    {
        $id = AchievementWinner::factory()
            ->for($this->user)
            ->claimed()
            ->create()
            ->id;

        $response = $this->postJson($this->url, ['achievement_winner_id' => $id]);

        $response->assertUnprocessable();
        $this->assertDatabaseHas('achievement_winners', [
            'id' => $id,
            'is_claimed' => true
        ]);
    }

    public function testAchievementCanBeClaimed()
    {
        $id = AchievementWinner::factory()->for($this->user)->create()->id;

        $response = $this->postJson($this->url, ['achievement_winner_id' => $id]);

        $response->assertOk();
        $this->assertDatabaseHas('achievement_winners', [
            'id' => $id,
            'is_claimed' => true
        ]);
    }
}
