<?php

namespace Tests\Feature\User\Competition;

use App\Models\Competition;
use Tests\TestCase;

class LotterySummaryTest extends TestCase
{
    private $url = '/api/user/lotteries/summary';
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserAndLogin();
    }

    public function testLotterySummaryContainsUpcomingLotteries()
    {
        Competition::factory()->upcoming()->count(5)->create();
        $response = $this->getJson($this->url);
        $response->assertOk();
        $response->assertJsonCount(5, 'data.upcoming');
    }

    public function testLotterySummaryContainsRunningLotteries()
    {
        Competition::factory()
            ->hasAttached($this->user, [], 'entrants')
            ->started()
            ->count(5)
            ->create();

        $response = $this->getJson($this->url);

        $response->assertOk();
        $response->assertJsonCount(5, 'data.running');
    }

    public function testLotterySummaryContainsOpenLotteries()
    {
        Competition::factory()->started()->count(5)->create();
        $response = $this->getJson($this->url);
        $response->assertOk();
        $response->assertJsonCount(5, 'data.open');
    }

    public function testLotterySummaryContainsUnrevealedLotteries()
    {
        Competition::factory()
            ->hasAttached($this->user, [], 'entrants')
            ->ended()
            ->count(5)
            ->create();

        Competition::factory()
            ->hasAttached($this->user, ['competition_revealed' => true], 'entrants')
            ->ended()
            ->count(5)
            ->create();

        $response = $this->getJson($this->url);

        $response->assertOk();
        $response->assertJsonCount(5, 'data.unrevealed');
    }
}