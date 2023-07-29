<?php

namespace Tests\Feature\Auth;

use App\Models\Competition;
use App\Models\User;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    private $registerUrl = '/api/auth/register';

    /**
     * @dataProvider invalidOpenRegistrationDataProvider
     */
    public function testOpenRegistrationFailsWithInvalidData(array $overrides)
    {
        config(['app.open_registration' => true]);
        $requestData = $this->makeRegisterRequestData($overrides);

        $response = $this->postJson($this->registerUrl, $requestData);
        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 1);
    }

    public function invalidOpenRegistrationDataProvider(): array
    {
        return [
            'Empty email' => [ ['email' => ''] ],
            'Invalid email' => [ ['email' => 'invalid-email#gmail'] ],
            'Empty password' => [ ['password' => ''] ],
        ];
    }

    /**
     * @dataProvider openRegistrationDuplicateDataProvider
     */
    public function testOpenRegistrationFailsWithDuplicateData(string $key)
    {
        config(['app.open_registration' => true]);
        $user = User::factory()->create();
        $requestData = $this->makeRegisterRequestData([$key => $user[$key]]);

        $response = $this->postJson($this->registerUrl, $requestData);
        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 2);
    }

    public function openRegistrationDuplicateDataProvider(): array
    {
        return [
            'Duplicate email' => ['email'],
            'Dupliate username' => ['username'],
        ];
    }

    public function testOpenRegistrationSucceedsWithValidData()
    {
        config(['app.open_registration' => true]);

        $requestData = $this->makeRegisterRequestData();

        $response = $this->postJson($this->registerUrl, $requestData);
        $response->assertOk();
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('role_user', 2);
    }

    public function testClosedRegistrationFailsWithNonMatchingActivationCode()
    {
        config(['app.open_registration' => false]);
        $user = User::factory()->nonActivated()->create();
        $requestData = $this->makeRegisterRequestData([
            'email' => $user->email,
            'activation_code' => 'ZZZZ-999'
        ]);

        $response = $this->postJson($this->registerUrl, $requestData);
        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 2);
    }

    public function testClosedRegistrationSucceedsWithValidData()
    {
        config(['app.open_registration' => false]);
        $user = User::factory()->nonActivated()->create();

        $requestData = $this->makeRegisterRequestData([
            'email' => $user->email,
            'activation_code' => $user->activation_code,
        ]);

        $response = $this->postJson($this->registerUrl, $requestData);
        $response->assertOk();
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('role_user', 2);
    }

    public function testClosedRegistrationFailsWithNonMatchingExternalId()
    {
        config(['app.open_registration' => false]);
        config(['auth.external_id_account_activation' => true]);
        $user = User::factory()->nonActivatedExternalId()->create();
        $requestData = $this->makeRegisterRequestData([
            'email' => $user->email,
            'external_id' => 'ZZZZ-999'
        ]);
        $response = $this->postJson($this->registerUrl, $requestData);
        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 2);
    }

    public function testClosedRegistrationSucceedsWithValidExternalId()
    {
        config(['app.open_registration' => false]);
        config(['auth.external_id_account_activation' => true]);
        $user = User::factory()->nonActivatedExternalId()->create();
        $requestData = $this->makeRegisterRequestData([
            'email' => $user->email,
            'external_id' => $user->external_id,
        ]);
        $response = $this->postJson($this->registerUrl, $requestData);
        $response->assertOk();
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('role_user', 2);
    }

    public function testPasswordRequiresLowercaseCharValidation()
    {
        config(['auth.password_validation.requires_lowercase_char' => true]);
        $requestData = $this->makeRegisterRequestData(['password' => 'TEST12345']);

        $response = $this->postJson($this->registerUrl, $requestData);
        $data = json_decode($response->getContent());

        $response->assertUnprocessable();
        $this->assertEquals($data->errors->password[0], __('auth')['password_requires_lowercase']);
    }

    public function testPasswordRequiresUppercaseCharValidation()
    {
        config(['auth.password_validation.requires_uppercase_char' => true]);
        $requestData = $this->makeRegisterRequestData(['password' => 'test12345']);

        $response = $this->postJson($this->registerUrl, $requestData);
        $data = json_decode($response->getContent());

        $response->assertUnprocessable();
        $this->assertEquals($data->errors->password[0], __('auth')['password_requires_uppercase']);
    }

    public function testPasswordRequiresNumberValidation()
    {
        config(['auth.password_validation.requires_number' => true]);
        $requestData = $this->makeRegisterRequestData(['password' => 'Test@test']);

        $response = $this->postJson($this->registerUrl, $requestData);
        $data = json_decode($response->getContent());

        $response->assertUnprocessable();
        $this->assertEquals($data->errors->password[0], __('auth')['password_requires_number']);
    }

    public function testPasswordRequiresSymbolValidation()
    {
        config(['auth.password_validation.requires_symbol' => true]);
        $requestData = $this->makeRegisterRequestData(['password' => 'Test1345']);

        $response = $this->postJson($this->registerUrl, $requestData);
        $data = json_decode($response->getContent());

        $response->assertUnprocessable();
        $this->assertEquals($data->errors->password[0], __('auth')['password_requires_symbol']);
    }

    public function testUsernameGenerationWorks()
    {
        config(['app.open_registration' => true]);
        config(['app.generate_usernames' => true]);

        $requestData = $this->makeRegisterRequestData();
        unset($requestData['username']);
        $response = $this->postJson($this->registerUrl, $requestData);

        $response->assertOk();
    }

    public function testUserIsEnteredIntoCorrectCompetitionsAfterRegistering()
    {
        config(['app.open_registration' => true]);

        Competition::factory()->autoEnter()->started()->create();
        $this->assertDatabaseCount('competition_participants', 0);

        $requestData = $this->makeRegisterRequestData();
        $response = $this->postJson($this->registerUrl, $requestData);

        $response->assertOk();
        $this->assertDatabaseCount('competition_participants', 1);
    }

    private function makeRegisterRequestData(array $overrides = []): array
    {
        $defaults = [
            'email' => 'test@test.com',
            'paypal_email' => 'paypal@test.com',
            'username' => 'Test',
            'password' => 'Test@12345',
        ];
        return array_merge($defaults, $overrides);
    }
}
