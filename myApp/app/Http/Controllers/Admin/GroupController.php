<?php

namespace App\Http\Controllers\Admin;

use App\Classes\GroupManager;
use App\Models\Group;
use App\Http\Controllers\Controller;
use App\Http\Requests\Group\AddUsersToGroupRequest;
use App\Http\Requests\Group\CreateGroupRequest;
use App\Http\Requests\Group\DeleteGroupRequest;
use App\Http\Requests\Group\RemoveUsersFromGroupRequest;
use Illuminate\Http\Request;
use Validator;

/**
 * @group Admin API - Groups
 *
 * Admin APIs for managing groups.
 *
 * Groups provide a simple way to organise users. They are hierarchical, meaning
 * that a group can contain subgroups, and those subgroups can contain further
 * subgroups etc. This allows for a more complex structure to be put in place if
 * desired.
 *
 * <strong>Note</strong>: A group can contain <em>either</em> subgroups
 * <em>or</em> users, but not both.
 */
class GroupController extends Controller
{
    /**
     * Create Group
     *
     * Creates a new group using the values provided.
     *
     * @bodyParam name string required Max: 255. The name of the group. Example: London Group
     * @bodyParam is_default_group bool If you set this to 1, the previous default group will have it's `is_default_group` value set to 0. The members of the previous default group will not be moved. Example: 0
     * @bodyParam parent_id int The ID of another group to be the parent of this group. The parent group must not contain users however it may be a parent of other groups. Example: 10
     * @bodyParam metadata json Metadata for the group in JSON format. If a value is null for any key-value pairs contained in the metadata, then that key-value pair will be ignored. Example: [{"title": "Some Metadata Title"}, {"url": "https://www.example.com"}]
     *
     * @responseFile 200 resources/responses/Admin/Group/create.json
     * @responseFile 422 resources/responses/Admin/Group/create-422.json
     */
    public function create(CreateGroupRequest $req)
    {
        $result = GroupManager::create($req->validated());
        return response()->success(__('group')['created_success'], $result);
    }

    /**
     * Update Group
     *
     * Updates the specified group with the values provided. All fields besides `id` are optional and any fields which are omitted will remain unchanged. If you wish to update a group's name and nothing else, then you only need to include the `name` field in your request.
     *
     * @bodyParam id int required The ID of the group being updated. Example: 1
     * @bodyParam name string Max: 255. The name of the group. Example: London Group
     * @bodyParam is_default_group bool If you set this to 1, the previous default group will have it's `is_default_group` value set to 0. The members of the previous default group will not be moved. Example: 0
     * @bodyParam parent_id int The ID of another group to be the parent of this group. The parent group must not contain users however it may be a parent of other groups. Example: 10
     * @bodyParam default_group_id int The ID of another group which will take this group's place as the default group. This field is required only if the value of `is_default_group` is being changed from 1 to 0. Example: 6
     * @bodyParam metadata json Metadata for the group in JSON format. If a value is null for any key-value pairs contained in the metadata, then that key-value pair will be ignored. Example: [{"title": "Some Metadata Title"}, {"url": "https://www.example.com"}]
     *
     * @responseFile 200 resources/responses/Admin/Group/update.json
     * @responseFile 422 resources/responses/Admin/Group/update-422.json
     */
    public function update(Request $req)
    {
        $v = Validator::make($req->all(), [
            'id' => 'required|exists:groups,id,deleted_at,NULL',
            'name' => 'string',
            'parent_id' => 'bail',
            'is_default_group' => 'boolean',
            'metadata' => 'json'
        ]);

        // Ensures that the parent_id exists and is not a group containing users.
        $v->sometimes(
            'parent_id',
            'exists:groups,id,deleted_at,NULL|unique:group_user,group_id',
            function ($input) {
                return $input->parent_id != 0;
            }
        );

        $v->sometimes('parent_id', 'valid_parent_id', function ($input) {
            return isset($input->id) && $input->parent_id != 0;
        });


        $v->sometimes('default_group_id', 'required|different:id|exists:groups,id,deleted_at,NULL', function($input) {
            $data = Group::find($input->id);
            $defaultGroup = Group::where(['is_default_group' => 1])->get()->count();
            if ($defaultGroup == 0 && isset($input->is_default_group) && $input->is_default_group == 0) {
                return true;
            }
            if (isset($input->is_default_group) &&
                gettype($data) == 'object' &&
                $input->is_default_group == 0) {
                return $data->is_default_group == 1;
            }
            if (isset($input->default_group_id) && $input->default_group_id == $input->default_group_id) {
                return true;
            }
        });

        if ($v->fails()) {
            return response()->error(null, $v->messages());
        }

        $result = GroupManager::update($req);
        if ($result == 'nothing-updated') {
            $result = GroupManager::group($req);
            return response()->success(__('nothing_updated'), $result);
        } else if ($result != 'groupnotfound') {
            return response()->success(__('group')['update_success'], $result);
        } else {
            return response()->error(__('handler')['validation_exception']);
        }
    }

    /**
     * Delete Group
     *
     * Deletes the group specified by the `id` parameter. If the group contains sub-groups or users,
     * another group must be specified to serve as a backup. Sub-groups and/or users in the group being
     * deleted will be moved to the backup group.
     *
     * @bodyParam id int required The ID of the group being deleted. Example: 1
     * @bodyParam backup_group_id int The ID of another group which is serve as a backup group. Example: 2
     * @bodyParam default_group_id int The ID of another group which will become the default group. This parameter is required when you delete the default group. Example: 3
     *
     * @responseFile 200 resources/responses/Admin/Group/delete.json
     * @responseFile 422 resources/responses/Admin/Group/delete-422.json
     */
    public function delete(DeleteGroupRequest $req)
    {
        $group = Group::find($req->id);

        if ($group->isNotEmpty() && $req->backup_group_id === NULL) {
            return response()->error(__('group')['not_empty']);
        }

        if ($group->is_default_group && $req->default_group_id === NULL) {
            return response()->error(__('group')['is_default_required']);
        }

        if ($group->isNotEmpty()) {
            $backupGroup = Group::find($req->backup_group_id);

            // Need to refactor this to make sure backupGroup is not
            // a descendent of this group at any level (not just immediate).
            if ($backupGroup->parent_id === $group->id) {
                return response()->error(__('group')['deleting_backup_parent_group']);
            }

            try {
                $group->moveContentsTo($backupGroup);
            } catch (Exception $e) {
                return response()->error($e->getMessage());
            }
        }

        if ($group->is_default_group) {
            Group::find($req->default_group_id)->setAsDefault();
        }

        $group->delete();
        return response()->success(__('group')['delete_success']);
    }

    /**
     * List Groups
     *
     * Lists all groups.
     *
     * @responseFile 200 resources/responses/Admin/Group/list.json
     */
    public function list()
    {
        $groups = Group::with('children', 'users', 'meta')->whereNull('parent_id')->get();
        return response()->success(__('group_list_success'), $groups);
    }

    /**
     * Get Group
     *
     * Retrieves the group specified by `id`.
     *
     * @bodyParam id int required The ID of the group. Example: 1
     *
     * @responseFile 200 resources/responses/Admin/Group/get.json
     */
    public function get(Request $req)
    {
        $req->validate(['id' => 'required|exists:groups,id,deleted_at,NULL']);

        $group = Group::with('children', 'users', 'meta')->find($req->id);
        return response()->success(__('group_success'), $group);
    }

    /**
     * Add Users To Group
     *
     * Add users to the group with the given `id`.
     *
     * @bodyParam id int required The ID of the group the user will be added to. The group must not be a parent group. Example: 1
     * @bodyParam users int[] required An array of user IDs to add to the group. Example: [1, 2, 3]
     *
     * @responseFile 200 resources/responses/Admin/Group/add-user.json
     * @responseFile 422 resources/responses/Admin/Group/add-user-422.json
     */
    public function addUser(AddUsersToGroupRequest $req)
    {
        Group::find($req->id)->users()->attach($req->users);
        return response()->success(__('group')['user_add_success']);
    }

    /**
     * Remove Users from Group
     *
     * Remove users from the group with the given `id`.
     *
     * @bodyParam id int required The ID of the group the user will be removed from. Example: 1
     * @bodyParam users int[] required An array of user IDs to remove from the group. Example: [1, 2, 3]
     *
     * @responseFile 200 resources/responses/Admin/Group/remove-user.json
     * @responseFile 422 resources/responses/Admin/Group/remove-user-422.json
     */
    public function removeUser(RemoveUsersFromGroupRequest $req)
    {
        Group::find($req->id)->users()->detach($req->users);
        return response()->success(__('group')['user_remove_success']);
    }
}
