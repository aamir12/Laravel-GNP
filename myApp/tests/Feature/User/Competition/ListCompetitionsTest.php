<?php

namespace Tests\Feature\User\Competition;

use App\Models\Competition;
use Tests\TestCase;

class ListCompetitionsTest extends TestCase
{
    private $url = '/api/user/competitions/list';
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserAndLogin();
    }

    public function testListCompetitions()
    {
        $competition = Competition::factory()->create(['is_lottery' => false]);
        $competition->entrants()->attach($this->user->id);
        $response = $this->getJson($this->url);
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }
}