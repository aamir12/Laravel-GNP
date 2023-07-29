<?php

namespace Tests\Feature\Admin\Achievement;

use Tests\TestCase;

class AchievementRoutesTest extends TestCase
{
    private $url = '/api/admin/achievements/';

    /**
     * @dataProvider endpointProvider
     */
    public function testAuthenticationRequired($method, $endpoint)
    {
        $url = $this->url . $endpoint;
        $response = $this->json($method, $url);
        $response->assertUnauthorized();
    }

    /**
     * @dataProvider endpointProvider
     */
    public function testAdminAuthorisationRequired($method, $endpoint)
    {
        $this->createUserAndLogin(false); // Login as regular non-admin user.
        $url = $this->url . $endpoint;
        $response = $this->json($method, $url);
        $response->assertForbidden();
    }

    public function endpointProvider()
    {
        return [
            'Create' => ['POST', 'create'],
            'List' => ['GET', 'list'],
            'Get' => ['GET', 'get'],
            'Update' => ['POST', 'update'],
            'Delete' => ['POST', 'delete'],
        ];
    }
}
