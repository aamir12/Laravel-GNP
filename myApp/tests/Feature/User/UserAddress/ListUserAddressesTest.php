<?php

namespace Tests\Feature\User\UserAddress;

use App\Models\UserAddress;
use Tests\TestCase;

class ListUserAddressesTest extends TestCase
{
    private $url = '/api/user/address/list';
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserAndLogin();
    }

    public function testListAddressesSucceedsWithEmptyList()
    {
        $response = $this->getJson($this->url);
        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    public function testListAddressesSucceeds()
    {
        UserAddress::factory()->count(10)->create(['user_id' => $this->user->id]);
        $response = $this->getJson($this->url);
        $response->assertOk();
        $response->assertJsonCount(10, 'data');
    }
}
