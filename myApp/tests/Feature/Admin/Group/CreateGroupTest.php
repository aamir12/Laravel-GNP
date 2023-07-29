<?php

namespace Tests\Feature\Admin\Group;

use App\Models\Group;
use Tests\TestCase;

class CreateGroupTest extends TestCase
{
    private $url = '/api/admin/groups/create';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidGroupDataProvider
     */
    public function testGroupCreationFailsWithInvalidData(array $overrides)
    {
        $requestData = $this->makeGroupRequestData($overrides);

        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(array_keys($overrides));
    }

    public function invalidGroupDataProvider(): array
    {
        return [
            'Empty name' => [ ['name' => ''] ],
            'Invalid parent_id' => [ ['parent_id' => 'invalid parent id'] ],
            'Nonexistant parent_id' => [ ['parent_id' => 100000] ],
            'Invalid is_default_group' => [ ['is_default_group' => 'invalid bool'] ],
            'Invalid metadata' => [ ['metadata' => 'invalid json'] ],
        ];
    }

    public function testGroupCreationFailsWithParentIdOfGroupContainingUsers()
    {
        $groupWithUsersId = Group::factory()->hasUsers(3)->create()->id;
        $requestData = $this->makeGroupRequestData(['parent_id' => $groupWithUsersId]);

        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('parent_id');
    }

    /**
     * @dataProvider validGroupDataProvider
     */
    public function testGroupCreationSucceedsWithValidData(array $overrides)
    {
        Group::factory()->default()->create();
        $requestData = $this->makeGroupRequestData($overrides);

        $response = $this->postJson($this->url, $requestData);

        $response->assertOk();
    }

    public function validGroupDataProvider(): array
    {
        return [
            'Valid data' => [[]],
            'Valid data with is_default_group' => [ ['is_default_group' => true] ],
            'Valid data with metadata' => [
                ['metadata' => '[{"title": "Group Title"},{"url": "https://www.example.com"}]']
            ],
        ];
    }

    public function testGroupCreationSucceedsWithValidParentId()
    {
        // Create group that contains subgroups.
        $groupId = Group::factory()
                        ->has(Group::factory()->count(3), 'children')
                        ->create()
                        ->id;

        $requestData = $this->makeGroupRequestData(['parent_id' => $groupId]);
        $response = $this->postJson($this->url, $requestData);

        $response->assertOk();
    }

    public function testCreatedGroupIsAlwaysSetAsDefaultIfItIsTheOnlyGroup()
    {
        $requestData = $this->makeGroupRequestData();

        $response = $this->postJson($this->url, $requestData);

        $response->assertOk();
        $this->assertDatabaseCount('groups', 1);
        $this->assertDatabaseHas('groups', ['is_default_group' => true]);
    }

    public function testThereCanOnlyBeOneDefaultGroup()
    {
        $initialDefaultGroupId = Group::factory()->default()->create()->id;
        $requestData = $this->makeGroupRequestData(['is_default_group' => true]);

        $response = $this->postJson($this->url, $requestData);

        $response->assertOk();
        $this->assertDatabaseCount('groups', 2);
        $this->assertDatabaseHas('groups', [
            'id' => $initialDefaultGroupId,
            'is_default_group' => false
        ]);
        $this->assertDatabaseHas('groups', ['is_default_group' => true]);
    }

    private function makeGroupRequestData(array $overrides = []): array
    {
        $defaults = [
            'name' => 'Test Group',
        ];

        return array_merge($defaults, $overrides);
    }
}