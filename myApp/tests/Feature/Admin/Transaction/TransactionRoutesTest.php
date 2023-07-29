<?php

namespace Tests\Feature\Admin\Transaction;

use Tests\TestCase;

class TransactionRoutesTest extends TestCase
{
    private $url = '/api/admin/transactions/';

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
            'Update' => ['POST', 'update']
        ];
    }
}