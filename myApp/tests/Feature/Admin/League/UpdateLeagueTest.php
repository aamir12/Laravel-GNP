<?php

namespace Tests\Feature\Admin\League;

use App\Models\League;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UpdateLeagueTest extends TestCase
{
    private $url = '/api/admin/leagues/update';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider updateInvalidLeagueProvider
     */
    public function testUpdateLeagueFailsWithInvalidData($overrides)
    {
        $requestData = $this->makeLeagueRequestData($overrides);
        $leagueData = League::factory()->create();
        $requestData['id'] = $leagueData->id;
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
    }

    public function updateInvalidLeagueProvider()
    {
        return [
            'Empty name' => [
                ['name' => ''],
            ],
            'Empty description' => [
                ['description' => ''],
            ],
            'Empty image' => [
                ['image' => ''],
            ],
            'Invalid aggregation period' => [
                ['score_aggregation_period' => 'test'],
            ],
            'Empty data' => [
                [
                    'name' => '',
                    'description' => '',
                    'owner_id' => '',
                    'score_aggregation_period' => '',
                    'type' => ''
                ],
            ],
            'Image as string' => [
                ['image' => 'test.png'],
            ],
        ];
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testUpdateLeagueFailsWithInvalidId($id)
    {
        $requestData = $this->makeLeagueRequestData(['id' => $id]);
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
    }

    /**
     * @dataProvider updateValidLeagueProvider
     */
    public function testUpdateLeagueSuccessWithValidData($overrides)
    {
        $requestData = $this->makeLeagueRequestData($overrides);
        $leagueData = League::factory()->create();
        $requestData['id'] = $leagueData->id;
        $response = $this->postJson($this->url, $requestData);
        $responseData = json_decode($response->getContent())->data;
        $response->assertOk();
        $this->assertEquals($responseData->name, $requestData['name']);
        $this->assertEquals($responseData->description, $requestData['description']);
    }

    public function updateValidLeagueProvider()
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
