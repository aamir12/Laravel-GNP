<?php

namespace Tests\Feature\Admin\Competition;

use App\Models\Competition;
use Tests\TestCase;

class GetCompetitionTest extends TestCase
{
    private $url = '/api/admin/competitions/get';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testGetCompetitionFailsWithInvalidData($id)
    {
        $response = $this->getJson($this->url . '?id=' . $id);
        $response->assertUnprocessable();
    }

    public function testGetCompetitionSucceedsWithValidData()
    {
        $id = Competition::factory()->create()->id;
        $response = $this->getJson($this->url . '?id=' . $id);
        $response->assertOk();
    }
}
