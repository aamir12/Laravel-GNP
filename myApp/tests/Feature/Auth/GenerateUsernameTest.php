<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class GenerateUsernameTest extends TestCase
{
    private $generateUsernameUrl = '/api/auth/generate-username';

    public function testGenerateUsernameWorks()
    {
        $response = $this->getJson($this->generateUsernameUrl);
        $response->assertOk();
        $response->assertJsonStructure(['data' => ['username']]);
    }
}
