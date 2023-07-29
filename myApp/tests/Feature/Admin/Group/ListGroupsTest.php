<?php

namespace Tests\Feature\Admin\Group;

use App\Models\Group;
use Tests\TestCase;

class ListGroupsTest extends TestCase
{
    private $url = '/api/admin/groups/list';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testListGroups()
    {
        Group::factory()->count(10)->create();
        $response = $this->getJson($this->url);

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
    }

    public function testListGroupsIncludesSubgroups()
    {
        Group::factory()
            ->has(Group::factory()->count(3), 'children')
            ->count(5)
            ->create();

        $response = $this->getJson($this->url);

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        $response->assertJsonCount(3, 'data.0.children');
    }

    public function testListGroupsExcludesSoftDeletedGroups()
    {
        $group = Group::factory()->create();
        $group->delete();

        $response = $this->getJson($this->url);

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }
}