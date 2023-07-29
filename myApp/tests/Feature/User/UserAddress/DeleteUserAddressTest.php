<?php

namespace Tests\Feature\User\UserAddress;

use App\Models\UserAddress;
use Tests\TestCase;

class DeleteUserAddressTest extends TestCase
{
    private $url = '/api/user/address/delete';
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserAndLogin();
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testDeleteAddressFailsWithInvalidId($id)
    {
        UserAddress::factory()->create(['user_id' => $this->user->id]);
        $response = $this->postJson($this->url, ['id' => $id]);
        $response->assertUnprocessable();
        $this->assertDatabaseCount('user_addresses', 1);
    }

    public function testDeleteAddressSucceedsWithValidId()
    {
        $address = UserAddress::factory()->create(['user_id' => $this->user->id]);
        $response = $this->postJson($this->url, ['id' => $address->id]);
        $response->assertOk();
        $this->assertModelMissing($address);
    }
}
