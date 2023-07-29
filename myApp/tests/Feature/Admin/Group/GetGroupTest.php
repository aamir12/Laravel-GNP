<?php

namespace Tests\Feature\Admin\Group;

use App\Models\Group;
use Tests\TestCase;

class GetGroupTest extends TestCase
{
    private $url = '/api/admin/groups/get';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testGetGroupFailsWithInvalidId($id)
    {
        Group::factory()->create();
        $response = $this->getJson($this->url . '?id=' . $id);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['id']);
    }

    public function testGetGroupFailsWithSoftDeletedGroup()
    {
        $group = Group::factory()->create();
        $group->delete();
        $response = $this->getJson($this->url . '?id=' . $group->id);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['id']);
    }

    public function testGetGroupSucceedsWithValidId()
    {
        $id = Group::factory()->create()->id;
        $response = $this->getJson($this->url . '?id=' . $id);

        $response->assertOk();
        $response->assertJsonPath('data.id', $id);
        $response->assertJsonStructure([
            'data' => [
                'children',
                'users'
            ]
        ]);
    }
}