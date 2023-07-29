<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LoginTest extends TestCase
{
    private $loginUrl = '/api/auth/login';
    private $loggedInUserUrl = '/api/auth/me';

    /**
     * @dataProvider invalidLoginDataProvider
     */
    // public function testLoginFailsWithInvalidData(array $requestData)
    // {
    //     User::factory()->create([
    //         'email' => 'test@test.com',
    //         'username' => 'Test123',
    //         'password' => bcrypt('Test@12345'),
    //     ]);

    //     $response = $this->postJson($this->loginUrl, $requestData);
    //     $response->assertUnprocessable();
    // }

    public function invalidLoginDataProvider(): array
    {
        return [
            'Empty username' => [ ['username' => '', 'password' => 'Test@12345'] ],
            'Empty password' => [ ['username' => 'Test123', 'password' => ''] ],
        ];
    }

    /**
     * @dataProvider incorrectCredentialsDataProvider
     */
    // public function testLoginFailsWithIncorrectCredentials(array $requestData)
    // {
    //     Artisan::call('migrate:fresh');
    //     Artisan::call('passport:install');
    //     User::factory()->create([
    //         'email' => 'test@test.com',
    //         'username' => 'Test123',
    //         'password' => bcrypt('Test@12345'),
    //     ]);

    //     $response = $this->postJson($this->loginUrl, $requestData);
    //     $response->assertUnauthorized();

    //     Artisan::call('migrate:fresh');
    // }

    public function incorrectCredentialsDataProvider(): array
    {
        return [
            'Wrong username' => [ ['username' => 'WrongUsername', 'password' => 'Test@12345'] ],
            'Wrong password' => [ ['username' => 'Test123', 'password' => 'Wrong@12345'] ],
        ];
    }

    /**
     * @dataProvider correctCredentialsDataProvider
     */
    // public function testLoginSucceedsWithCorrectCredentials(array $requestData)
    // {
    //     Artisan::call('migrate:fresh');
    //     Artisan::call('passport:install');
    //     User::factory()->create([
    //         'email' => 'test@test.com',
    //         'username' => 'Test123',
    //         'password' => bcrypt('Test@12345'),
    //     ]);

    //     $response = $this->postJson($this->loginUrl, $requestData);
    //     $response->assertOk();

    //     Artisan::call('migrate:fresh');
    // }

    public function correctCredentialsDataProvider(): array
    {
        return [
            'Email as identifer' => [ ['username' => 'test@test.com', 'password' => 'Test@12345'] ],
            'Username as identifier' => [ ['username' => 'Test123', 'password' => 'Test@12345'] ],
        ];
    }

    public function testGetLoggedInUserFailsIfNotLoggedIn()
    {
        User::factory()->create();
        $response = $this->getJson($this->loggedInUserUrl);
        $response->assertUnauthorized();
    }

    public function testGetLoggedInUserSucceedsWhenLoggedIn()
    {
        $this->createUserAndLogin();
        $response = $this->getJson($this->loggedInUserUrl);
        $response->assertOk();
    }
}