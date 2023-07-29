<?php

namespace Tests\Feature\Admin\League;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;

class CreateLeagueTest extends TestCase
{
    private $url = '/api/admin/leagues/create';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider createInvalidLeagueProvider
     */
    public function testCreateLeagueFailsWithInvalidData($overrides)
    {
        $requestData = $this->makeLeagueRequestData($overrides);
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
    }

    public function createInvalidLeagueProvider()
    {
        return [
            'Empty name' => [
                ['name' => '']
            ],
            'Empty description' => [
                ['description' => '']
            ],
            'Empty image' => [
                ['image' => '']
            ],
            'Image as string' => [
                ['image' => 'test.png']
            ],
            'Invalid aggregation period' => [
                ['score_aggregation_period' => 'test']
            ],
            'Empty data' =>  [
                [
                    'name' => '',
                    'description' => '',
                    'owner_id' => '',
                    'score_aggregation_period' => '',
                    'type' => ''
                ]
            ],
        ];
    }

    /**
     * @dataProvider createValidLeagueProvider
     */
    public function testCreateLeagueSucceedsWithValidData($overrides)
    {
        $requestData = $this->makeLeagueRequestData($overrides);
        $response = $this->postJson($this->url, $requestData);
        $responseData = json_decode($response->getContent())->data;
        $response->assertOk();
        $this->assertEquals($responseData->name, $requestData['name']);
        $this->assertEquals($responseData->description, $requestData['description']);
        $this->assertDatabaseCount('leagues', 1);
    }

    public function createValidLeagueProvider()
    {
        return [
            'Real image' => [ ['image' => UploadedFile::fake()->image('test.jpg')] ],
            'Valid data' => [ [] ],
        ];
    }

    private function makeLeagueRequestData(array $overrides = []): array
    {
        $defaults = [
            'name' => 'Mallory Gleichner',
            'description' => 'Delectus commodi sit harum harum et totam modi. Quibusdam ex et reprehenderit rerum quibusdam et nobis sed. A aliquid enim nisi. Voluptatem aliquam modi id eligendi.',
            'owner_id' => 5,
            'score_aggregation_period' => 'daily',
            'type' => 'Public'
        ];
        return array_merge($defaults, $overrides);
    }
}
