<?php

namespace Tests\Feature\Admin\Achievement;

use Tests\TestCase;

class CreateAchievementTest extends TestCase
{
    private $url = '/api/admin/achievements/create';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidAchievementDataProvider
     */
    public function testCreateAchievementFailsWithInvalidData(array $overrides)
    {
        $requestData = $this->makeAchievementRequestData($overrides);
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
        $this->assertDatabaseCount('achievements', 0);
    }

    public function invalidAchievementDataProvider(): array
    {
        return [
            'Empty Name' => [ ['name' => ''] ],
            'Empty Description' => [ ['description' => ''] ],
        ];
    }

    public function testCreateAchievementSucceedsWithValidData()
    {
        $requestData = $this->makeAchievementRequestData();

        $response = $this->postJson($this->url, $requestData);

        $response->assertOk();
        $this->assertDatabaseCount('achievements', 1);
        $this->assertDatabaseHas('achievements', [
            'name' => $requestData['name'],
            'description' => $requestData['description'],
        ]);
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
