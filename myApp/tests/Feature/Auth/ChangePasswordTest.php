<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    private $changePasswordUrl = '/api/auth/password/change';

    public function testChangePasswordFailsIfNotLoggedIn()
    {
        User::factory()->create();

        $response = $this->postJson($this->changePasswordUrl, [
            'password' => 'Test@123',
            'cpassword' => 'Test@123'
        ]);
        $response->assertUnauthorized();
    }

    /**
     * @dataProvider invalidPasswordDataProvider
     */
    public function testChangePasswordFailsWithInvalidData(array $requestData)
    {
        $this->createUserAndLogin();
        $response = $this->postJson($this->changePasswordUrl, $requestData);
        $response->assertUnprocessable();
    }

    public function invalidPasswordDataProvider()
    {
        return [
            'Empty Password' => [ ['password' => '', 'cpassword' => 'Test@123'] ],
            'Empty confirm password' => [ ['password' => 'Test@123' , 'cpassword' => ''] ],
            'Non matching passwords' => [ ['password' => 'Test@123' , 'cpassword' => 'Test' ] ],
        ];
    }

    public function testChangePasswordSucceedsWhenLoggedIn()
    {
        $this->createUserAndLogin();
        $response = $this->postJson($this->changePasswordUrl, [
            'password' => 'Test@123',
            'cpassword' => 'Test@123'
        ]);

        $response->assertOk();
    }
}