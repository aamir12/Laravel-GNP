<?php

namespace Tests\Feature\Admin\League;

use App\Models\League;
use Tests\TestCase;

class ListLeagueTest extends TestCase
{
    private $url = '/api/admin/leagues/list';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testListLeague()
    {
        League::factory()->count(10)->create();
        $response = $this->getJson($this->url);
        $response->assertOk();
    }
}
