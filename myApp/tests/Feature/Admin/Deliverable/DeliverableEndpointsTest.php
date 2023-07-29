<?php

namespace Tests\Feature\Admin\Deliverable;

use Tests\TestCase;

class DeliverableEndpointsTest extends TestCase
{
    private $url = '/api/admin/deliverables/';

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
            'List' => ['GET', 'list'],
            'Get' => ['GET', 'get'],
            'Update' => ['POST', 'update'],
        ];
    }
}
