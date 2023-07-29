<?php

namespace Tests\Feature\Admin\Competition;

use App\Models\Competition;
use Tests\TestCase;

class ListCompetitionsTest extends TestCase
{
    private $url = '/api/admin/competitions/list';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testListCompetitionsSucceedsWithEmptyList()
    {
        $response = $this->getJson($this->url);
        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    public function testListCompetitionsSucceeds()
    {
        Competition::factory()->count(10)->create();

        $response = $this->getJson($this->url);

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
    }
}
