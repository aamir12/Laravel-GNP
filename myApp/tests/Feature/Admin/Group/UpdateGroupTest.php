<?php

namespace Tests\Feature\Admin\Group;

use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class UpdateGroupTest extends TestCase
{
    private $url = '/api/admin/groups/update';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider updateGroupProvider
     */
    public function testUpdateGroup($groupData, $type, $expectedStatusCode)
    {
        $groupData = $groupData->toArray();
        $groupToUpdate = Group::factory()->create();
        $groupData['id'] = $groupToUpdate->id;

        if ($type == 'non-numeric-id') {
             $groupData['id'] = 'not-an-id';
        }
        if ($type == 'invalid-id') {
            $groupData['id'] = -1;
        }
        if ($type == 'soft-deleted-id') {
            $newGroup = Group::factory()->create(['deleted_at' => date('Y-m-d H:i:s')]);
            $groupData['id'] = $newGroup->id;
        }
        if ($type == 'parent-with-users') {
            $roleId = Role::firstWhere('name', 'user')->id;
            $parentGroup = Group::factory()->create();
            $parentGroup->each(function ($gr) use($roleId)  {
                $user = User::factory()->create();
                $gr->users()->attach($user->id);
                $user->roles()->attach($roleId);
            });
            $groupData['parent_id'] =  $parentGroup->id;
        }
        if ($type == 'self-parent-id') {
            $groupData['parent_id'] =  $groupData['id'];
        }
        if ($type == 'parent-id-of-self-child') {
            $childGroup = Group::factory()->create(['parent_id' => $groupToUpdate->id]);
            $groupData['parent_id'] = $childGroup->id;
        }
        if ($type == 'parent-id-soft-deleted') {
            $parentGroup = Group::factory()->create(['deleted_at' => date('Y-m-d H:i:s')]);
            $groupData['parent_id'] = $parentGroup->id;
        }
        if ($type == 'success-data') {
            Group::factory()->create(['is_default_group' => 1]);
        }
        $response = $this->postJson($this->url, $groupData);
        $response->assertStatus($expectedStatusCode);
    }

    public function updateGroupProvider()
    {
        $this->createApplication();
        return [
            'Non numeric id' => [
                Group::factory()->make(),
                'non-numeric-id',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'Invalid id' => [
                Group::factory()->make(),
                'invalid-id',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'Soft delete id' => [
                Group::factory()->make(),
                'soft-deleted-id',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'Empty name' => [
                Group::factory()->make(['name' => '']),
                'empty-name',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'Non numeric parent id' => [
                Group::factory()->make(['parent_id' => 'test-id']),
                'non-numeric-parent-id',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'Invalid parent id' => [
                Group::factory()->make(['parent_id' => -1]),
                'invalid-parent-id',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'Parent with users' => [
                Group::factory()->make(),
                'parent-with-users',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'Self parent id' => [
                Group::factory()->make(),
                'self-parent-id',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'Parent id of self child' => [
                Group::factory()->make(),
                'parent-id-of-self-child',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'Parent id soft deleted' => [
                Group::factory()->make(),
                'parent-id-soft-deleted',
                Response::HTTP_UNPROCESSABLE_ENTITY
            ],
            'Invalid is default value' => [
                Group::factory()->make(['is_default_group' => 5]),
                'invalid-is-default-value',
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
            'Success data' => [
                Group::factory()->make(),
                'success-data',
                Response::HTTP_OK
            ],
            'Success data default group' => [
                Group::factory()->make(['is_default_group' => 1]),
                'success-data-default-group',
                Response::HTTP_OK
            ]
        ];
    }
}