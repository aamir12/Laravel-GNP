<?php

namespace Tests\Feature\User\KPI;

use Tests\TestCase;

class KPIRoutesTest extends TestCase
{
    private $urlPrefix = '/api/user/kpi/';

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
    public function testAdminAuthorisationRequired($method, $endpoint)
    {
        $this->createUserAndLogin(true); // Login as admin user.
        $url = $this->urlPrefix . $endpoint;
        $response = $this->json($method, $url);
        $response->assertForbidden();
    }

    public function routeProvider()
    {
        return [
            'List' => ['GET', 'list'],
        ];
    }
}