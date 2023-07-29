<?php

namespace Tests\Feature\Admin\Achievement;

use App\Models\Achievement;
use Tests\TestCase;

class DeleteAchievementTest extends TestCase
{
    private $url = '/api/admin/achievements/delete';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testDeleteAchievementFailsWithAlreadyDeletedInvalidData()
    {
        $achievement = Achievement::factory()->create();
        $achievement->delete();
        $response = $this->postJson($this->url, ['id' => $achievement->id]);
        $response->assertUnprocessable();
    }

    public function testDeleteAchievementSucceedsWithValidData()
    {
        $achievement = Achievement::factory()->create();
        $response = $this->postJson($this->url, ['id' => $achievement->id]);
        $response->assertOk();
    }

}
