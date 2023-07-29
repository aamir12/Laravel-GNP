<?php

namespace Tests\Feature\User\Achievement;

use Tests\TestCase;

class AchievementRoutesTest extends TestCase
{
    private $urlPrefix = '/api/user/achievements/';

    /**
     * @dataProvider routeProvider
     */
    public function testAuthenticationRequired($method, $endpoint)
    {
        $url = $this->urlPrefix . $endpoint;
        $response = $this->json($method, $url);
        $response->assertUnauthorized();
    }

    /**
     * @dataProvider routeProvider
     */
    public function testUserAuthorisationRequired($method, $endpoint)
    {
        $this->createUserAndLogin(true); // Login as admin user.
        $url = $this->urlPrefix . $endpoint;
        $response = $this->json($method, $url);
        $response->assertForbidden();
    }

    public function routeProvider()
    {
        return [
            'Claim' => ['POST', 'claim'],
        ];
    }
}