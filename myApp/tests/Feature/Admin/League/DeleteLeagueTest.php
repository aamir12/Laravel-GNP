<?php

namespace Tests\Feature\Admin\League;

use App\Models\League;
use Tests\TestCase;

class DeleteLeagueTest extends TestCase
{
    private $url = '/api/admin/leagues/delete';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testDeleteLeagueFailsWithInvalidId($invalidId)
    {
        League::factory()->create();
        $response = $this->postJson($this->url, ['id' => $invalidId]);
        $response->assertUnprocessable();
    }

    public function testDeleteLeagueSucceedsWithValidId()
    {
        $leagueData = League::factory()->create();
        $response = $this->postJson($this->url, ['id' => $leagueData->id]);
        $response->assertOk();
    }
}
