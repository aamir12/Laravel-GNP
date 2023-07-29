<?php

namespace Tests\Feature\Admin\KPI;

use App\Models\User;
use Tests\TestCase;

class CreateKPITest extends TestCase
{
    private $urlPrefix = '/api/admin/kpi/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testBulkCreateKpiFailsWithEmptyData()
    {
        User::factory()->create(['email' => 'kpi@test.com', 'external_id' => 'EXT-123']);
        $requestData = ['kpi' => []];

        $response = $this->postJson($this->urlPrefix . 'create', $requestData);
        $response->assertUnprocessable();
    }

    public function testBulkCreateKpiFailsWithNoUserIdentifier()
    {
        User::factory()->create(['email' => 'kpi@test.com', 'external_id' => 'EXT-123']);
        $requestData = ['kpi' => [$this->makeKpiRequestData()]];

        $response = $this->postJson($this->urlPrefix . 'create', $requestData);
        $response->assertUnprocessable();
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testBulkCreateKpiFailsWithInvalidUserId($userId)
    {
        User::factory()->create(['email' => 'kpi@test.com', 'external_id' => 'EXT-123']);
        $requestData = ['kpi' => [$this->makeKpiRequestData(['user_id' => $userId])]];

        $response = $this->postJson($this->urlPrefix . 'create', $requestData);
        $response->assertUnprocessable();
    }

    /**
     * @dataProvider invalidEmailProvider
     */
    public function testBulkCreateKpiFailsWithInvalidEmail($email)
    {
        User::factory()->create(['email' => 'kpi@test.com', 'external_id' => 'EXT-123']);
        $requestData = ['kpi' => [$this->makeKpiRequestData(['email' => $email])]];

        $response = $this->postJson($this->urlPrefix . 'create', $requestData);
        $response->assertUnprocessable();
    }

    public function invalidEmailProvider(): array
    {
        return [
            'Not a string' => [1],
            'Invalid email' => ['not-a-valid-email'],
            'Nonexistant email' => ['nonexistant@test.com'],
        ];
    }

    /**
     * @dataProvider invalidExternalIdProvider
     */
    public function testBulkCreateKpiFailsWithInvalidExternalId($externalId)
    {
        User::factory()->create(['email' => 'kpi@test.com', 'external_id' => 'EXT-123']);
        $requestData = ['kpi' => [$this->makeKpiRequestData(['external_id' => $externalId])]];

        $response = $this->postJson($this->urlPrefix . 'create', $requestData);
        $response->assertUnprocessable();
    }

    public function invalidExternalIdProvider(): array
    {
        return [
            'Not a string' => [1],
            'Nonexistant external ID' => ['XYZ-789'],
        ];
    }

    public function testBulkCreateKpiFailsWithAllUserIdentifiersPresent()
    {
        $user = User::factory()->create(['email' => 'kpi@test.com', 'external_id' => 'EXT-123']);
        $overrides['user_id'] = $user->id;
        $overrides['email'] = $user->email;
        $overrides['external_id'] = $user->external_id;
        $requestData = ['kpi' => [$this->makeKpiRequestData($overrides)]];

        $response = $this->postJson($this->urlPrefix . 'create', $requestData);
        $response->assertUnprocessable();
    }

    /**
     * @dataProvider invalidKpiDataProvider
     */
    public function testBulkCreateKpiFailsWithInvalidData(array $overrides)
    {
        $user = User::factory()->create(['email' => 'kpi@test.com', 'external_id' => 'EXT-123']);
        $overrides['user_id'] = $user->id;
        $requestData = ['kpi' => [$this->makeKpiRequestData($overrides)]];

        $response = $this->postJson($this->urlPrefix . 'create', $requestData);
        $response->assertUnprocessable();
    }

    public function invalidKpiDataProvider()
    {
        $this->createApplication();

        return [
            'Empty value' => [ ['value' => ''] ],
            'Non-numeric value' => [ ['value' => 'Invalid'] ],
            'Empty weight' => [ ['weight' => ''] ],
            'Non-numeric weight' => [ ['weight' => 'Invalid'] ],
            'Non-date timestamp' => [ [ 'timestamp' => 100] ],
            'Non-json metadata' => [ ['metadata' => 'Invalid'] ],
        ];
    }

    public function testBulkCreateKpiFailsWithPartiallyValidData()
    {
        $user = User::factory()->create(['email' => 'kpi@test.com', 'external_id' => 'EXT-123']);
        $requestData = ['kpi' => [
            $this->makeKpiRequestData(['user_id' => $user->id]), // Valid
            $this->makeKpiRequestData() // Invalid
        ]];

        $response = $this->postJson($this->urlPrefix . 'create', $requestData);
        $response->assertUnprocessable();
    }

    public function testBulkCreateKpiSucceedsWithValidData()
    {
        $user1 = User::factory()->create(['email' => 'user1@test.com', 'external_id' => 'EXT-123']);
        $user2 = User::factory()->create(['email' => 'user2@test.com', 'external_id' => 'EXT-456']);
        $user3 = User::factory()->create(['email' => 'user3@test.com', 'external_id' => 'EXT-789']);
        $requestData = ['kpi' => [
            $this->makeKpiRequestData(['user_id' => $user1->id]),
            $this->makeKpiRequestData(['email' => $user2->email]),
            $this->makeKpiRequestData(['external_id' => $user3->external_id]),
        ]];
        unset($requestData['kpi'][1]['weight']);
        unset($requestData['kpi'][2]['timestamp']);

        $response = $this->postJson($this->urlPrefix . 'create', $requestData);

        $response->assertOk();
        $data = json_decode($response->getContent())->data;
        foreach ($data as $key => $value) {
            $this->assertEquals($value->value, $requestData['kpi'][$key]['value']);
        }
    }

    public function testUsersCanBeAutoCreatedOnKpiSubmissionIfConfigVarIsEnabled()
    {
        config(['kpi.auto_create_users_on_kpi_submit' => true]);

        $user1 = User::factory()->create(['email' => 'user1@test.com', 'external_id' => 'EXT-123']);
        $user2 = User::factory()->create(['email' => 'user2@test.com', 'external_id' => 'EXT-456']);
        $user3 = User::factory()->create(['email' => 'user3@test.com', 'external_id' => 'EXT-789']);
        $missingExternalId = 'EXT-MISSING';


        $requestData = ['kpi' => [
            $this->makeKpiRequestData(['user_id' => $user1->id]),
            $this->makeKpiRequestData(['email' => $user2->email]),
            $this->makeKpiRequestData(['external_id' => $user3->external_id]),
            $this->makeKpiRequestData(['external_id' => $missingExternalId]),
        ]];

        $response = $this->postJson($this->urlPrefix . 'create', $requestData);

        $response->assertOk();

        $response->assertJsonCount(4, 'data');
        $this->assertDatabaseHas('users', ['external_id' => $missingExternalId]);
    }

    private function makeKpiRequestData(array $overrides = []): array
    {
        $defaults = [
            'value' => '10.43',
            'weight' => 4.7,
            'timestamp' => '2021-02-10 20:54'
        ];
        return array_merge($defaults, $overrides);
    }
}