<?php

namespace Tests\Feature\User\Prize;

use App\Models\Prize;
use App\Models\Stock;
use App\Models\Winner;
use Tests\TestCase;

class ClaimPrizeTest extends TestCase
{
    private $url = '/api/user/prizes/claim';

    public function testAuthenticationRequired()
    {
        $response = $this->postJson($this->url);
        $response->assertUnauthorized();
    }

    public function testUserAuthorisationRequired()
    {
        $this->createUserAndLogin(true);
        $response = $this->postJson($this->url);
        $response->assertForbidden();
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testClaimPrizeFailsWithInvalidId($id)
    {
        $user = $this->createUserAndLogin();
        $prize = Prize::factory()->for(Stock::factory())->create();
        Winner::factory()->for($user)->for($prize)->create();

        $response = $this->postJson($this->url, ['id' => $id]);

        $response->assertUnprocessable();
    }

    public function testUserCannotClaimAPrizeTheyHaventWon()
    {
        $this->createUserAndLogin();
        $prize = Prize::factory()->for(Stock::factory())->create();
        Winner::factory()->for($prize)->claimed()->create();

        $response = $this->postJson($this->url, ['id' => $prize->id]);

        $response->assertUnprocessable();
        $response->assertJsonPath('message', __('prize')['not_prize_winner']);
    }

    public function testUserCannotClaimAPrizeTheyHaveAlreadyClaimed()
    {
        $user = $this->createUserAndLogin();
        $prize = Prize::factory()->for(Stock::factory())->create();
        Winner::factory()->for($user)->for($prize)->claimed()->create();

        $response = $this->postJson($this->url, ['id' => $prize->id]);

        $response->assertUnprocessable();
        $response->assertJsonPath('message', __('prize')['already_claimed']);
    }

    public function testUserCanClaimAPrizeTheyHaveWon()
    {
        $user = $this->createUserAndLogin();
        $prize = Prize::factory()->for(Stock::factory())->create();
        Winner::factory()->for($user)->for($prize)->create();

        $response = $this->postJson($this->url, ['id' => $prize->id]);

        $response->assertOk();
        $response->assertJsonPath('message', __('prize')['success_claim']);
    }
}
