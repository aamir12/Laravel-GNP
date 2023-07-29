<?php

namespace Tests\Feature\Admin\Group;

use App\Models\Group;
use Tests\TestCase;

class DeleteGroupTest extends TestCase
{
    private $url = '/api/admin/groups/delete';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testDeleteGroupFailsWithInvalidId($invalidId)
    {
        Group::factory()->create();

        $response = $this->postJson($this->url, ['id' => $invalidId]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['id']);
    }

    public function testDeleteGroupFailsWithAlreadySoftDeletedGroup()
    {
        $group = Group::factory()->create();
        $group->delete();

        $response = $this->postJson($this->url, ['id' => $group->id]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['id']);
    }

    public function testAttemptingToDeleteTheDefaultGroupRequiresNewDefaultGroupId()
    {
        $group = Group::factory()->default()->create();

        $response = $this->postJson($this->url, ['id' => $group->id]);

        $response->assertUnprocessable();
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testDeletingTheDefaultGroupFailsWithInvalidNewDefaultGroupId($invalidId)
    {
        $groupId = Group::factory()->default()->create()->id;

        $response = $this->postJson($this->url, [
            'id' => $groupId,
            'default_group_id' => $invalidId
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['default_group_id']);
    }

    public function testDeletingTheDefaultGroupFailsWhenNewDefaultGroupIdRefersToSoftDeletedGroup()
    {
        $groupId = Group::factory()->default()->create()->id;
        $deletedGroup = Group::factory()->create();
        $deletedGroup->delete();

        $response = $this->postJson($this->url, [
            'id' => $groupId,
            'default_group_id' => $deletedGroup->id
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['default_group_id']);
    }

    public function testDeleteNonDefaultEmptyGroupSucceedsWithValidId()
    {
        $groupId = Group::factory()->create()->id;

        $response = $this->postJson($this->url, ['id' => $groupId]);

        $response->assertOk();
        $this->assertSoftDeleted('groups', ['id' => $groupId]);
    }

    public function testDeleteEmptyDefaultGroupSucceedsWithValidNewDefaultGroupId()
    {
        $groupId = Group::factory()->default()->create()->id;
        $newDefaultGroupid = Group::factory()->create()->id;

        $response = $this->postJson($this->url, [
            'id' => $groupId,
            'default_group_id' => $newDefaultGroupid,
        ]);

        $response->assertOk();
        $this->assertSoftDeleted('groups', ['id' => $groupId]);

        // Assert that deleted group is no longer default.
        $this->assertDatabaseHas('groups', [
            'id' => $groupId,
            'is_default_group' => false,
        ]);
        $this->assertDatabaseHas('groups', [
            'id' => $newDefaultGroupid,
            'is_default_group' => true,
        ]);
    }

    public function testDeleteGroupContainingUsersSucceedsWithValidBackupId()
    {
        $groupId = Group::factory()->hasUsers(3)->create()->id;
        $backupGroupId = Group::factory()->create()->id;

        $response = $this->postJson($this->url, [
            'id' => $groupId,
            'backup_group_id' => $backupGroupId,
        ]);

        $response->assertOk();
        $this->assertSoftDeleted('groups', ['id' => $groupId]);

        // Assert that users have been transferred to the backup group.
        $this->assertDatabaseMissing('group_user', ['group_id' => $groupId]);
        $this->assertDatabaseHas('group_user', ['group_id' => $backupGroupId]);
    }

    public function testDeleteGroupContainingSubgroupsSucceedsWithValidBackupId()
    {
        $groupId = Group::factory()
            ->has(Group::factory()->count(3), 'children')
            ->create()
            ->id;
        $backupGroupId = Group::factory()->create()->id;

        $response = $this->postJson($this->url, [
            'id' => $groupId,
            'backup_group_id' => $backupGroupId,
        ]);

        $response->assertOk();
        $this->assertSoftDeleted('groups', ['id' => $groupId]);

        // Assert that subgroups have been transferred to the backup group.
        $this->assertDatabaseMissing('groups', ['parent_id' => $groupId]);
        $this->assertDatabaseHas('groups', ['parent_id' => $backupGroupId]);
    }
}