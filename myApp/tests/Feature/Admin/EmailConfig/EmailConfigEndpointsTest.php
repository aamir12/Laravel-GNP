<?php

namespace Tests\Feature\Admin\EmailConfig;

use Tests\TestCase;

class EmailConfigEndpointsTest extends TestCase
{
    private $url = '/api/admin/email-config/';

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
    public function testAdminAuthorizationRequired($method, $endpoint)
    {
        $this->createUserAndLogin(false); // Login as regular non-admin user.
        $url = $this->url . $endpoint;
        $response = $this->json($method, $url);
        $response->assertForbidden();
    }

    public function endpointProvider()
    {
        return [
            'Get' => ['GET', 'get'],
            'List' => ['GET', 'list'],
            'Update' => ['POST', 'update'],
        ];
    }
}
