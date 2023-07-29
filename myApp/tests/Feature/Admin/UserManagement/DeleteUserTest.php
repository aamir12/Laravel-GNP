<?php

namespace Tests\Feature\Admin\UserManagement;

use App\Models\User;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    private $url = '/api/admin/users/delete';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testDeleteUserFailsWithInvalidId($id)
    {
        User::factory()->nonActivated()->create();
        $response = $this->postJson($this->url, ['id' => $id]);
        $response->assertUnprocessable();
    }

    public function testCannotDeleteActivatedUser()
    {
        $user = User::factory()->create();
        $response = $this->postJson($this->url, ['id' => $user->id]);
        $response->assertUnprocessable();
    }

    public function testCanDeleteNonActivatedUser()
    {
        $user = User::factory()->nonActivated()->create();
        $response = $this->postJson($this->url, ['id' => $user->id]);
        $response->assertOk();
    }
}
