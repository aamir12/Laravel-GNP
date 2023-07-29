<?php

namespace Tests\Feature\User\KPI;

use App\Models\Score;
use App\Models\User;
use Tests\TestCase;

class ListKPITest extends TestCase
{
    private $urlPrefix = '/api/user/kpi/';

    public function testListKpiOnlyListsLoggedInUsersKpi()
    {
        $loggedInUserId = $this->createUserAndLogin(false)->id;
        $otherUserId = User::factory()->create()->id;
        Score::factory()->count(5)->create(['user_id' => $loggedInUserId]);
        Score::factory()->count(10)->create(['user_id' => $otherUserId]);

        $response = $this->getJson($this->urlPrefix . 'list');

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    }
}