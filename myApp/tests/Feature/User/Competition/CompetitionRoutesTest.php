<?php

namespace Tests\Feature\User\Competition;

use Tests\TestCase;

class CompetitionRoutesTest extends TestCase
{
    private $urlPrefix = '/api/user/';

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
    public function testUserAuthorisationRequired($method, $endpoint)
    {
        $this->createUserAndLogin(true); // Login as admin user.
        $url = $this->urlPrefix . $endpoint;
        $response = $this->json($method, $url);
        $response->assertForbidden();
    }

    public function routeProvider()
    {
        return [
            'Enter Competition' => ['POST', 'competitions/enter'],
            'Enter Lottery' => ['POST', 'lotteries/enter'],
            'Reveal' => ['POST', 'competitions/reveal'],
            'List Competitions' => ['GET' , 'competitions/list'],
            'List Lotteries' => ['GET' , 'lotteries/list'],
            'List Open Lotteries' => ['GET' , 'lotteries/list/open'],
        ];
    }
}