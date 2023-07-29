<?php

namespace Tests\Feature\Admin\Winner;

use App\Models\Winner;
use Tests\TestCase;

class ListWinnersTest extends TestCase
{
    private $url = '/api/admin/winners/list';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testListWinnersSucceedsWithEmptyList()
    {
        $response = $this->getJson($this->url);
        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    public function testListWinnersSucceeds()
    {
        Winner::factory()->count(10)->create();

        $response = $this->getJson($this->url);

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
    }

    public function testListWinnersCanBeFilteredByIsClaimed()
    {
        Winner::factory()->count(2)->create();
        Winner::factory()->claimed()->count(8)->create();

        $response = $this->getJson($this->url . '?is_claimed=0');
        $response->assertOk();
        $response->assertJsonCount(2, 'data');

        $response = $this->getJson($this->url . '?is_claimed=1');
        $response->assertOk();
        $response->assertJsonCount(8, 'data');
    }
}
