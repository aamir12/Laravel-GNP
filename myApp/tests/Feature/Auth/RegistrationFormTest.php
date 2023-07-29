<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegistrationFormTest extends TestCase
{
    private $registerUrl = '/register';

    public function testRegistrationPageCanBeRendered()
    {
        $response = $this->get($this->registerUrl);
        $response->assertOk();
    }

    public function testExternalIdFieldIsPresentWhenEnvVarIsSet()
    {
        config(['auth.external_id_label' => 'Test Label']);
        $response = $this->get($this->registerUrl);
        $response->assertOk();
        $response->assertSee('Test Label');
    }

    public function testExternalIdFieldIsNotPresentWhenEnvVarIsNotSet()
    {
        config(['auth.external_id_label' => '']);
        $response = $this->get($this->registerUrl);
        $response->assertOk();
        $response->assertDontSee('external_id');
    }

    public function testNameFieldsArePresentWhenUsernameGenerationIsDisabled()
    {
        config(['app.generate_usernames' => false]);
        $response = $this->get($this->registerUrl);
        $response->assertOk();
        $response->assertSee('first_name');
        $response->assertSee('last_name');
    }

    public function testNameFieldsAreNotPresentWhenUsernameGenerationIsEnabled()
    {
        config(['app.generate_usernames' => true]);
        $response = $this->get($this->registerUrl);
        $response->assertOk();
        $response->assertDontSee('first_name');
        $response->assertDontSee('last_name');
    }
}
