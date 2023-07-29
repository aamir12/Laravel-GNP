<?php

namespace Tests\Feature\Admin\Competition;

use Tests\TestCase;

class CompetitionRoutesTest extends TestCase
{
    private $urlPrefix = '/api/admin/competitions/';

    /**
     * @dataProvider routeProvider
     */
    public function testAuthenticationRequired(string $method, string $endpoint)
    {
        $url = $this->urlPrefix . $endpoint;
        $response = $this->json($method, $url);
        $response->assertUnauthorized();
    }

    /**
     * @dataProvider routeProvider
     */
    public function testAdminAuthorisationRequired(string $method, string $endpoint)
    {
        $this->createUserAndLogin(false); // Login as regular non-admin user.
        $url = $this->urlPrefix . $endpoint;
        $response = $this->json($method, $url);
        $response->assertForbidden();
    }

    public function routeProvider(): array
    {
        return [
            'Create' => ['POST', 'create'],
            'Get' => ['GET', 'get'],
            'List' => ['GET', 'list'],
            'Update' => ['POST', 'update'],
        ];
    }
}
