<?php

namespace Tests\Feature\Admin\Achievement;

use Tests\TestCase;

class ListAchievementTest extends TestCase
{
    private $url = '/api/admin/achievements/list';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testListAchievementSucceedsWithValidData()
    {
        $response = $this->getJson($this->url);
        $response->assertOk();
    }

    public function testListAchievementSucceedsWithAnEmptyList()
    {
        $response = $this->getJson($this->url);
        $response->assertOk();
        $this->assertDatabaseCount('achievements', 0);
    }
}
