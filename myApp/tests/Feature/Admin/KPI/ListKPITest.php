<?php

namespace Tests\Feature\Admin\KPI;

use App\Models\Score;
use App\Models\User;
use Tests\TestCase;

class ListKPITest extends TestCase
{
    private $urlPrefix = '/api/admin/kpi/';

    /**
     * @dataProvider invalidIdProvider
     */
    public function testListKpiFailsWithInvalidUserId($userId)
    {
        $this->createUserAndLogin(true);
        $response = $this->getJson($this->urlPrefix . 'list?user_id=' . $userId);
        $response->assertUnprocessable();
    }

    public function testListKpiSucceedsWithValidUserId()
    {
        $this->createUserAndLogin(true);
        $userId1 = User::factory()->create()->id;
        $userId2 = User::factory()->create()->id;
        Score::factory()->count(5)->create(['user_id' => $userId1]);
        Score::factory()->count(5)->create(['user_id' => $userId2]);

        $response = $this->getJson($this->urlPrefix . 'list?user_id=' . $userId1);

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    }

    public function testListKpiSucceedsWithNoUserId()
    {
        $this->createUserAndLogin(true);
        $userId1 = User::factory()->create()->id;
        $userId2 = User::factory()->create()->id;
        Score::factory()->count(5)->create(['user_id' => $userId1]);
        Score::factory()->count(10)->create(['user_id' => $userId2]);

        $response = $this->getJson($this->urlPrefix . 'list');

        $response->assertOk();
        $response->assertJsonCount(15, 'data');
    }
}