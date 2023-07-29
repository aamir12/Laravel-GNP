<?php

namespace Tests\Feature\Admin\Branding;

use Tests\TestCase;

class BrandingEndpointsTest extends TestCase
{
    private $url = '/api/admin/branding/';

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
            'Get' => ['GET', 'get'],
            'Update' => ['POST', 'update'],
        ];
    }
}
