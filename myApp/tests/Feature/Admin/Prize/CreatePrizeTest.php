<?php

namespace Tests\Feature\Admin\Prize;

use App\Models\Competition;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CreatePrizeTest extends TestCase
{
    private $urlPrefix = '/api/admin/prizes/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testPrizeCreationFailsWithEmptyInput()
    {
        $response = $this->postJson($this->urlPrefix . 'create', []);
        $response->assertUnprocessable();
        $this->assertDatabaseCount('prizes', 0);
    }

    /**
     * @dataProvider invalidPrizeDataProvider
     */
    public function testCreatePrizeFailsWithInvalidData($overrides)
    {
        $requestData = $this->makePrizeRequestData($overrides);

        $response = $this->postJson($this->urlPrefix . 'create', $requestData);
        $response->assertUnprocessable();
        $this->assertDatabaseCount('prizes', 0);
    }

    /**
     * @dataProvider validPrizeDataProvider
     */
    public function testCreatePrizeSucceedsWithValidData($overrides)
    {
        $requestData = $this->makePrizeRequestData($overrides);

        $response = $this->postJson($this->urlPrefix . 'create', $requestData);

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['stock']]);
        $this->assertDatabaseCount('prizes', 1);
    }

    /**
     * @dataProvider invalidPrizeDataProvider
     */
    public function testBulkCreatePrizeFailsWithInvalidData($overrides)
    {
        $requestData = ['prizes' => []];
        $requestData['prizes'][] = $this->makePrizeRequestData($overrides);
        $requestData['prizes'][] = $this->makePrizeRequestData($overrides);

        $response = $this->postJson($this->urlPrefix . 'bulk-create', $requestData);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('prizes', 0);
    }

    public function testBulkCreatePrizeFailsIfASinglePrizeIsInvalid()
    {
        $requestData = ['prizes' => []];
        $requestData['prizes'][] = $this->makePrizeRequestData(); // Valid Prize
        $requestData['prizes'][] = $this->makePrizeRequestData(); // Valid Prize
        $requestData['prizes'][] = $this->makePrizeRequestData(); // Valid Prize
        $requestData['prizes'][] = $this->makePrizeRequestData(['name' => '']); // Invalid Prize

        $response = $this->postJson($this->urlPrefix . 'bulk-create', $requestData);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('prizes', 0);
    }

    /**
     * @dataProvider validPrizeDataProvider
     */
    public function testBulkCreatePrizeSucceedsWithValidData($overrides)
    {
        $requestData = ['prizes' => []];
        $requestData['prizes'][] = $this->makePrizeRequestData($overrides);
        $requestData['prizes'][] = $this->makePrizeRequestData($overrides);

        $response = $this->postJson($this->urlPrefix . 'bulk-create', $requestData);

        $response->assertOk();
        $this->assertDatabaseCount('prizes', 2);
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

    public function validPrizeDataProvider(): array
    {
        return [
            'Valid data' => [[]],
            'Valid cash prize data' => [ ['type' => 'cash', 'currency' => 'GBP', 'amount' => 10] ],
            'Valid data with image' => [ ['image' => UploadedFile::fake()->image('test.png')] ],
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