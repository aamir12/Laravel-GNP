<?php

namespace Tests\Feature\Client\KPI;

use App\Models\User;
use Laravel\Passport\Passport;
use Laravel\Passport\Client;
use Tests\TestCase;

class AddKpiTest extends TestCase
{
    private $urlPrefix = '/api/client/kpi/';

    /**
     * @dataProvider routeProvider
     */
    public function testAuthenticationRequired($method, $endpoint)
    {
        $url = $this->urlPrefix . $endpoint;
        $response = $this->json($method, $url);
        $response->assertUnauthorized();
    }

    /**
     * @dataProvider routeProvider
     */
    public function testCannotBeAccessedByUser($method, $endpoint)
    {
        $this->createUserAndLogin(false);
        $url = $this->urlPrefix . $endpoint;
        $response = $this->json($method, $url);
        $response->assertUnauthorized();
    }

    public function routeProvider()
    {
        return [
            'Bulk Create' => ['POST', 'create'],
        ];
    }

    public function testClientCanAddKpi()
    {
        Passport::actingAsClient(Client::factory()->create());

        $user1 = User::factory()->create(['email' => 'user1@test.com', 'external_id' => 'EXT123']);
        $user2 = User::factory()->create(['email' => 'user2@test.com', 'external_id' => 'EXT456']);
        $user3 = User::factory()->create(['email' => 'user3@test.com', 'external_id' => 'EXT789']);

        $requestData = ['kpi' => [
            $this->makeKpiRequestData(['user_id' => $user1->id]),
            $this->makeKpiRequestData(['email' => $user2->email]),
            $this->makeKpiRequestData(['external_id' => $user3->external_id]),
        ]];

        $response = $this->postJson($this->urlPrefix . 'create', $requestData);

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
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