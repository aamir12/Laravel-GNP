<?php

namespace Tests\Feature\Admin\KPI;

use Tests\TestCase;

class KPIRoutesTest extends TestCase
{
    private $urlPrefix = '/api/admin/kpi/';

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
        $this->createUserAndLogin(false); // Login as regular non-admin user.
        $url = $this->urlPrefix . $endpoint;
        $response = $this->json($method, $url);
        $response->assertForbidden();
    }

    public function routeProvider()
    {
        return [
            'Bulk Create' => ['POST', 'create'],
            'List' => ['GET', 'list'],
        ];
    }
}