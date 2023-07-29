<?php

namespace Tests\Feature\User\UserAddress;

use App\Models\UserAddress;
use Tests\TestCase;

class GetUserAddressTest extends TestCase
{
    private $url = '/api/user/address/get';
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserAndLogin();
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testGetAddressFailsWithInvalidId($id)
    {
        $response = $this->getJson($this->url . '?id=' . $id);
        $response->assertUnprocessable();
    }

    public function testGetAddressSucceedsWithValidId()
    {
        $address = UserAddress::factory()->create(['user_id' => $this->user->id]);
        $response = $this->getJson($this->url . '?id=' . $address->id);

        $response->assertOk();
        $response->assertJsonPath('data.user_id', $this->user->id);
    }
}
