<?php

namespace Tests\Feature\User\UserAddress;

use App\Models\UserAddress;
use Tests\TestCase;

class UpdateUserAddressTest extends TestCase
{
    private $url = '/api/user/address/update';
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserAndLogin();
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testUpdateAddressFailsWithInvalidId($id)
    {
        $requestData = $this->makeUserAddressRequestData(['id' => $id]);
        UserAddress::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
    }

    /**
     * @dataProvider updateInvalidUserAddressProvider
     */
    public function testUpdateAddressFailsWithInvalidData($overrides)
    {
        $requestData = $this->makeUserAddressRequestData($overrides);
        $requestData['id'] = UserAddress::factory()->create(['user_id' => $this->user->id])->id;

        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
    }

    public function updateInvalidUserAddressProvider(){
        return [
            'Empty Name' => [
                ['name' => '']
            ],
            'Empty address line 1' => [
                ['address_line_1' => '']
            ],
            'Empty town' => [
                ['town' => '']
            ],
            'Empty postcode' => [
                ['postcode' => '']
            ],
            'Empty country' => [
                ['country' => '']
            ]

        ];
    }

    /**
     * @dataProvider updateValidUserAddressProvider
     */
    public function testUpdateAddressSucceedsWithValidData($overrides)
    {
        $requestData = $this->makeUserAddressRequestData($overrides);
        $requestData['id'] = UserAddress::factory()->create(['user_id' => $this->user->id])->id;

        $response = $this->postJson($this->url, $requestData);

        $response->assertOk();
        $response->assertJsonPath('data.name', $requestData['name']);
        $response->assertJsonPath('data.country', $requestData['country']);
        $this->assertDatabaseHas('user_addresses', $requestData);
    }

    public function updateValidUserAddressProvider(){
        $this->createApplication();
        return [
            'Real name' => [
                ['name' => 'Test user']
            ],
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
