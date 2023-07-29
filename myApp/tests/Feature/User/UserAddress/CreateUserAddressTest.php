<?php

namespace Tests\Feature\User\UserAddress;

use Tests\TestCase;

class CreateUserAddressTest extends TestCase
{
    private $url = '/api/user/address/create';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin();
    }

    /**
     * @dataProvider createInvalidUserAddressProvider
     */
    public function testCreateAddressFailsWithInvalidData($overrides)
    {
        $requestData = $this->makeUserAddressRequestData($overrides);
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
    }

    public function createInvalidUserAddressProvider()
    {
        return [
            'Empty name' => [
                ['name' => '']
            ],
            'Empty Address Line 1' => [
                ['address_line_1' => '']
            ],
            'Empty town' => [
                ['town' => '']
            ],
            'Empty postcode'=>[
                ['postcode' => '']
            ],
            'Empty country'=>[
                ['country' => '']
            ],
            'Empty data' =>  [
                [
                    'name' => '',
                    'address_line_1' => '',
                    'town' => '',
                    'postcode' => '',
                    'country' => ''
                ]
            ],
        ];
    }

    /**
     * @dataProvider createValidUsesAddressProvider
     */
    public function testCreateAddressSucceedsWithValidData($overrides)
    {
        $requestData = $this->makeUserAddressRequestData($overrides);

        $response = $this->postJson($this->url, $requestData);

        $response->assertOk();
        $response->assertJsonPath('data.name', $requestData['name']);
        $response->assertJsonPath('data.postcode', $requestData['postcode']);
        $this->assertDatabaseCount('user_addresses', 1);
    }

    public function createValidUsesAddressProvider()
    {
        return [
            'Valid data' => [
                []
            ],
        ];
    }

    private function makeUserAddressRequestData(array $overrides = []): array
    {
        $defaults = [
            'name' => 'Mallory Gleichner',
            'address_line_1' => 'H 45, Navin Nagar, Mumbai',
            'town' => 'Mumbai',
            'postcode' => '462023',
            'country' => 'India'
        ];
        return array_merge($defaults, $overrides);
    }
}
