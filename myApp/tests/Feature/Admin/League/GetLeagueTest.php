<?php

namespace Tests\Feature\Admin\League;

use App\Models\League;
use Tests\TestCase;

class GetLeagueTest extends TestCase
{
    private $url = '/api/admin/leagues/get';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testGetLeagueFailsWithInvalidId($id)
    {
        $response = $this->getJson($this->url . '?id=' . $id);
        $response->assertUnprocessable();
    }

    public function testGetLeagueSucceeds()
    {
        $league = League::factory()->create(['type' => 'Public']);
        $response = $this->getJson($this->url . '?id=' . $league->id);
        $response->assertOk();
    }
}
