<?php

namespace Tests\Feature\Admin\UserManagement;

use Tests\TestCase;

class UserManagementRoutesTest extends TestCase
{
    private $urlPrefix = '/api/admin/users/';

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
            'Bulk Create' => ['POST', 'bulk-create'],
            'Get' => ['GET', 'get'],
            'List' => ['GET', 'list'],
            'Bulk Update' => ['POST', 'bulk-update'],
            'Delete' => ['POST', 'delete'],
        ];
    }
}
