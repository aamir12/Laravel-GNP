<?php

namespace Tests\Feature\Admin\Prize;

use Tests\TestCase;

class PrizeRoutesTest extends TestCase
{
    private $urlPrefix = '/api/admin/prizes/';

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
            'Create' => ['POST', 'create'],
            'Bulk Create' => ['POST', 'bulk-create'],
            'Get' => ['GET', 'get'],
            'List' => ['GET' ,'list'],
            'Update' => ['POST', 'update'],
            'Delete' => ['POST', 'delete'],
        ];
    }
}