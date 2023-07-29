<?php

namespace Tests\Feature\User\Competition;

use App\Models\Competition;
use Tests\TestCase;

class ListLotteriesTest extends TestCase
{
    private $url = '/api/user/lotteries/list';
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserAndLogin();
    }

    public function testListLotteries()
    {
        $competition = Competition::factory()->lottery()->create();
        $competition->entrants()->attach($this->user->id);
        $response = $this->getJson($this->url);
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function testListOpenLotteries()
    {
        Competition::factory()->started()->lottery()->create();
        $response = $this->getJson($this->url . '/open');
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function testListRunningLotteries()
    {
        $competition = Competition::factory()->lottery()->started()->create();
        $competition->entrants()->attach($this->user->id);
        $response = $this->getJson($this->url . '/running');
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function testListClosedLotteries()
    {
        $competition = Competition::factory()->lottery()->ended()->create();
        $competition->entrants()->attach($this->user->id);
        $response = $this->getJson($this->url . '/closed');
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function testListUnrevealedLotteries()
    {
        $competition = Competition::factory()->lottery()->ended()->create();
        $competition->entrants()->attach($this->user->id, ['competition_revealed' => 0]);
        $response = $this->getJson($this->url . '/unrevealed');
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }
}
