<?php

namespace Tests\Feature\Admin\Achievement;

use App\Models\Achievement;
use Tests\TestCase;

class UpdateAchievementTest extends TestCase
{
    private $url = '/api/admin/achievements/update';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidAchievementDataProvider
     */
    public function testUpdateAchievementFailsWithInvalidData(array $overrides)
    {
        $achievement = Achievement::factory()->create();
        $requestData = $this->makeAchievementRequestData($overrides);
        $requestData['id'] = $achievement->id;
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
    }

    public function invalidAchievementDataProvider(): array
    {
        return [
            'Empty Name' => [ ['name' => ''] ],
            'Empty Description' => [ ['description' => ''] ],
        ];
    }

    public function testUpdateAchievementSucceedsWithValidData()
    {
        $achievement = Achievement::factory()->create();
        $requestData = $this->makeAchievementRequestData();
        $requestData['id'] = $achievement->id;
        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();
    }

    private function makeAchievementRequestData(array $overrides = []): array
    {
        $defaults = [
            'name' => 'Test',
            'description' => 'Test Achievement'
        ];
        return array_merge($defaults, $overrides);
    }
}
