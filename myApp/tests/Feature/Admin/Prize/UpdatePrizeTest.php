<?php

namespace Tests\Feature\Admin\Prize;

use App\Models\Competition;
use App\Models\Prize;
use App\Models\Stock;
use Tests\TestCase;

class UpdatePrizeTest extends TestCase
{
    private $url = '/api/admin/prizes/update';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidPrizeDataProvider
     */
    public function testUpdatePrizeFailsWithInvalidData($requestData)
    {
        $requestData['id'] = Prize::factory()->create()->id;
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
        // TODO: Assert that prize record is unchanged.
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testUpdatePrizeFailsWithInvalidPrizeId($id)
    {
        $requestData = $this->makePrizeRequestData(['id' => $id]);
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
    }

    public function testUpdatePrizeSucceedsWithValidData($overrides = [])
    {
        $requestData = $this->makePrizeRequestData($overrides);
        $requestData['id'] = Prize::factory()->create()->id;

        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();
        $response->assertJsonStructure(['data' => ['stock']]);
    }

    public function invalidPrizeDataProvider(): array
    {
        return [
            'Empty name' => [ ['name' => ''] ],
            'Empty competition id' => [ ['competition_id' => ''] ],
            'Invalid competition id' => [ ['competition_id' => -1] ],
            'Non-numeric competition id' => [ ['competition_id' => 'Invalid'] ],
            'Image non-file' => [ ['image' => 'not-an-image.png'] ],
            'Empty type' => [ ['type' => ''] ],
            'Invalid type' => [ ['type' => 'Invalid'] ],
            'Empty amount' => [ ['type' => 'cash', 'currency' => 'GBP'] ],
            'Invalid amount' => [ ['type' => 'cash', 'currency' => 'GBP', 'amount' => 'Invalid'] ],
            'Empty currency' => [ ['type' => 'cash', 'currency' => '', 'amount' => 10] ],
            'Invalid reference' => [ ['reference' => '%Invalid%'] ],
            'Invalid zero winner count' => [ ['max_winners' => 0] ],
        ];
    }

    private function makePrizeRequestData(array $overrides = []): array
    {
        $defaults = [
            'name' => 'Test Prize',
            'competition_id' => Competition::factory()->create()->id,
            'type' => 'digital',
            'reference' => 'ABC123',
            'max_winners' => '10',
        ];

        return array_merge($defaults, $overrides);
    }
}