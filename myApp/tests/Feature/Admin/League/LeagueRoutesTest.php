<?php

namespace Tests\Feature\Admin\League;

use Tests\TestCase;

class LeagueRoutesTest extends TestCase
{
    private $url = '/api/admin/leagues/';

    /**
     * @dataProvider routeProvider
     */
    public function testAuthenticationRequired($method, $endpoint)
    {
        $url = $this->url . $endpoint;
        $response = $this->json($method, $url);
        $response->assertUnauthorized();
    }

    /**
     * @dataProvider routeProvider
     */
    public function testAdminAuthorisationRequired($method, $endpoint)
    {
        $this->createUserAndLogin(false); // Login as regular non-admin user.
        $url = $this->url . $endpoint;
        $response = $this->json($method, $url);
        $response->assertForbidden();
    }

    public function routeProvider()
    {
        return [
            'Create' => ['POST', 'create'],
            'Get' => ['GET', 'get'],
            'List' => ['GET', 'list'],
            'Update' => ['POST', 'update'],
        ];
    }
}
