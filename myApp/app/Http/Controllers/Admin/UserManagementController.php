<?php

namespace App\Http\Controllers\Admin;

use App\Classes\BasePeriod;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\BulkCreateUsersRequest;
use App\Http\Requests\User\BulkUpdateUsersRequest;
use App\Models\Meta;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;


/**
 * @group Admin API - User Management
 *
 * Admin APIs for managing users.
 *
 * Admins can invite new users to the platform via the bulk creation API. This
 * will trigger invitation emails to be sent out to the email addresses
 * provided.
 */
class UserManagementController extends Controller
{
    /**
     * List Users
     *
     * Lists all users.
     *
     * @responseFile 200 resources/responses/Admin/Users/list.json
     */
    public function list(Request $request)
    {
        $request->validate([
            'role' => 'exists:roles,name',
            'group_id' => 'exists:groups,id,deleted_at,NULL',
        ]);

        $metadata = Meta::extractMetadata($request->input());
        $nameArray = [];
        $contentArray = [];
        if ($metadata != null and count($metadata) > 0) {
            foreach ($metadata as $key => $columnVal) {
                $columnVal = (array)$columnVal;
                $key = array_keys($columnVal);
                $key = $key[0];
                $nameArray[] = $key;
                $contentArray[] = $columnVal[$key];
            }
        }

        $query = User::filter($request->input());

        if ($request->has('role')) {
            $query->withRole($request->input('role'));
        }

        if ($request->has('group_id')) {
            $query->inGroup($request->input('group_id'));
        }

        $query->with(['meta' => function ($query) use ($nameArray, $contentArray) {
            if (count($nameArray) > 0) {
                $query->whereIn('name', $nameArray);
                $query->whereIn('content', $contentArray);
            }
        }]);

        $users = $query->get();

        if ($metadata != null and count($metadata) > 0) {
            $users = $users->filter(function ($value, $key) {
                return $value->meta->isNotEmpty();
            })->values();
        }
        return response()->success(__('user_list_success'), $users->toArray());
    }

    /**
     * Bulk Create Users
     *
     * Creates one or more users using the values provided in an array.
     *
     * @bodyParam users[0][first_name] String Max: 100. Can be blank or Alphabets only Example: John
     * @bodyParam users[0][last_name] String Max: 100. Can be blank or Alphabets only Example: Chiswell
     * @bodyParam users[0][email] String Max: 191. Required if `external_id` is not present. Valid Email only Example: gevokihe@mail-guru.net
     * @bodyParam users[0][external_id] String Max: 255. Required if `email` is not present. Can be blank or any string, It must be unique Example: DFGHJ12345
     * @bodyParam users[0][paypal_email] String Max: 191. Can be blank or Valid Email only Example: gevokihe@mail-guru.net
     * @bodyParam users[0][phone] String Max: 20. Can be blank or a Valid Contact Number. Must start with a '+'. Example: +917778885555
     * @bodyParam users[0][timezone] String Max: 100. Can be blank or valid timezone only Example: Europe/London
     * @bodyParam users[0][dob] String Can be blank or valid date formatted as yyyy/mm/dd Example: 2019/08/22
     * @bodyParam users[0][metadata] JSON User Meta data Example: [{"title":"john"}, {"url" : "https://temp-mail.org/en/"}]
     * @bodyParam users[0][groups] Array Valid Group IDs, The groups must not contain subgroups. Example: [1]
     *
     * @responseFile 200 resources/responses/Admin/Users/bulk-create.json
     * @responseFile 422 resources/responses/Admin/Users/bulk-create-422.json
     */
    public function bulkCreate(BulkCreateUsersRequest $request)
    {
        $users = UserService::bulkCreateUsers($request->validated());
        return response()->success(__('auth')['register_success'], $users);
    }

    /**
     * Get User
     *
     * Retrieves the users specified by `id`.
     *
     * @bodyParam id int required The ID of the user. Example: 2
     *
     * @responseFile 200 resources/responses/Admin/Users/get.json
     * @responseFile 422 resources/responses/Admin/Users/get-422.json
     */
    public function get(Request $request)
    {
        $request->validate(['id' => 'required|exists:users']);
        $user = User::find($request->id);
        $user['score'] = BasePeriod::getScoresForLastNBasePeriods(5, $user->id);
        return response()->success(__('user_get_success'), $user);
    }
    /**
     * Bulk Update Users
     *
     * Updates the users specified in an array with the values provided.
     *
     * @bodyParam users[0][first_name] string Max: 100. Can be blank or Alphabets only Example: Pallavi
     * @bodyParam users[0][last_name] string Max: 100. Can be blank or Alphabets only Example: Biwal
     * @bodyParam users[0][email] string required Max: 191. Valid Email only Example: biwal.pallavi@gmail.com
     * @bodyParam users[0][phone] string Max: 20. Can be blank or Valid Contact Number starting with + only Example: +919090909090
     * @bodyParam users[0][external_id] string Max: 255. Can be blank or any string, It must be unique Example: DFGHJ12345
     * @bodyParam users[0][timezone] string Max: 100 Can be blank or valid timezone only Example:Europe/London
     * @bodyParam users[0][dob] string Can be blank or valid date only Example: 2000-09-01
     * @bodyParam users[0][metadata] json valid json only, <br>Note: for deleting just set that key value null like [{'title':null}] Example: [{"title":"john"}, {"url" : "https://temp-mail.org/en/"}]
     * @bodyParam users[0][id] int valid integer only Example: 1
     * @bodyParam users[0][role][0] int valid Role id Example: 1
     * @bodyParam users[0][groups] Array Of Valid Group ID, Group should not contain subgroups. Example: [1, 2]
     *
     * @responseFile 200 resources/responses/Admin/Users/bulk-update.json
     * @responseFile 422 resources/responses/Admin/Users/bulk-update-422.json
     */
    public function bulkUpdate(BulkUpdateUsersRequest $request)
    {
        $users = UserService::bulkUpdateUsers($request->validated());
        return response()->success(__('auth')['update_success'], $users);
    }

    /**
     * Delete User
     *
     * Delete the users specified by `id`.
     *
     * @bodyParam id int required The ID of the user. Example: 2
     *
     * @responseFile 200 resources/responses/Admin/Users/delete.json
     * @responseFile 422 resources/responses/Admin/Users/delete-422.json
     */
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id,is_activated,0'
        ]);
        User::find($request->id)->delete();
        return response()->success(__('user_delete_success'));
    }
}
