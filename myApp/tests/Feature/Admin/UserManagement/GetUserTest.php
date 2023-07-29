<?php

namespace Tests\Feature\Admin\UserManagement;

use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Tests\TestCase;

class GetUserTest extends TestCase
{
    private $url = '/api/admin/users/get';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testGetUserFailsWithInvalidId($id)
    {
        $user = User::factory()->create();
        RoleUser::create([
            'user_id' => $user->id,
            'role_id' => Role::firstWhere('name', 'user')->id,
        ]);

        $response = $this->getJson($this->url . '?id=' . $id);
        $response->assertUnprocessable();
    }

    public function testGetUserSucceedsWithValidId()
    {
        $user = User::factory()->create();
        RoleUser::create([
            'user_id' => $user->id,
            'role_id' => Role::firstWhere('name', 'user')->id,
        ]);

        $response = $this->getJson($this->url . '?id=' . $user->id);
        $response->assertOk();
        $response->assertJsonFragment(['id' => $user->id]);
    }
}
