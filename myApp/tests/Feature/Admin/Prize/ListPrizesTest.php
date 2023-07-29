<?php

namespace Tests\Feature\Admin\Prize;

use App\Models\Prize;
use Tests\TestCase;

class ListPrizesTest extends TestCase
{
    private $url = '/api/admin/prizes/list';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testListPrizesSucceedsWithEmptyList()
    {
        $response = $this->getJson($this->url);
        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    public function testListPrizesSucceeds()
    {
        Prize::factory()->count(10)->create();
        $response = $this->getJson($this->url);
        $response->assertOk();
        $response->assertJsonCount(10, 'data');
    }
}