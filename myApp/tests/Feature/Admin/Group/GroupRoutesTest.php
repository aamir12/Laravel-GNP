<?php

namespace Tests\Feature\Admin\Group;

use Tests\TestCase;

class GroupRoutesTest extends TestCase
{
    private $urlPrefix = '/api/admin/groups/';

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
            'Get' => ['GET', 'get'],
            'List' => ['GET' ,'list'],
            'Update' => ['POST', 'update'],
        ];
    }
}
