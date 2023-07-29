<?php

namespace App\Http\Controllers\Admin;

use App\Classes\LeagueManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\League\CreateLeagueRequest;
use App\Http\Requests\League\UpdateLeagueRequest;
use App\Models\League;
use Illuminate\Http\Request;

/**
 * @group Admin API - Leagues
 *
 * Admin APIs for managing leagues.
 *
 * Leagues provide a way for users to compare their scores against other users
 * in the same league. Each league consists of a list of users and their scores.
 *
 * Leagues can be <strong>public</strong> or <strong>private</strong>. However,
 * leagues created by admins are always <strong>public</strong>.
 */
class AdminLeagueController extends Controller
{
    /**
     * Create League
     *
     * Creates a new league using the values provided.
     *
     * @bodyParam name string required Max: 191. The name of the league. Example: Top Performers
     * @bodyParam description string required A description for the league. Example: A league for the best of the best!
     * @bodyParam image file A display image for the league. No-example
     * @bodyParam parent_id int The ID of another league which will be the parent of the league being created. Example: 2
     * @bodyParam score_aggregation_period string The period within which scores will be aggregated. Must be one of: daily/weekly/monthly.Example: daily
     * @bodyParam group_id int The ID of a group. If specified, users within that group will automatically be placed in the league being created. Example: 10
     *
     * @responseFile 200 resources/responses/Admin/League/create.json
     */
    public function create(CreateLeagueRequest $req)
    {
        $data = $req->validated();
        $result = LeagueManager::create($req);
        return $result;
    }

    /**
     * Update League
     *
     * Updates the specified league with the values provided.
     *
     * @bodyParam id integer required The ID of the league being updated. Example: 1
     * @bodyParam name string Max: 191. required The name of the league. Example: Top Performers
     * @bodyParam description string required A description for the league. Example: A league for the best of the best!
     * @bodyParam image file A display image for the league. No-example
     * @bodyParam parent_id int The ID of another league which will be the parent of the league being created. Example: 2
     * @bodyParam score_aggregation_period string The period within which scores will be aggregated. Must be one of: daily/weekly/monthly.Example: daily
     * @bodyParam group_id int The ID of a group. If specified, users within that group will automatically be placed in the league being created. Example: 10
     *
     * @responseFile 200 resources/responses/Admin/League/update.json
     */
    public function update(UpdateLeagueRequest $req)
    {
        $data = $req->validated();
        $result = LeagueManager::update($req);
        return $result;
    }

    /**
     * Get League
     *
     * Retrieves the league specified by `id`.
     *
     * @bodyParam id int required The ID of the league. Example: 1
     *
     * @responseFile 200 resources/responses/Admin/League/get.json
     * @responseFile 422 resources/responses/Admin/League/get-422.json
     */
    public function get(Request $req)
    {
        $req->validate([
            'id' => 'required|exists:leagues,id,deleted_at,NULL,type,Public'
        ]);
        $result = League::with('meta')->find($req->id);
        return response()->success(__('league')['found_success'], $result);
    }

    /**
     * List Leagues
     *
     * Lists all leagues.
     *
     * @responseFile 200 resources/responses/Admin/League/list.json
     */
    public function list()
    {
        $leagues = LeagueManager::listLeagues();
        return response()->success(__('league')['list_success'], $leagues);
    }

    /**
     * Delete League
     *
     * Deletes the league specified by `id`.
     *
     * @bodyParam id int required The ID of the league being deleted. Example: 1
     *
     * @responseFile 200 resources/responses/Admin/League/delete.json
     * @responseFile 422 resources/responses/Admin/League/delete-422.json
     */
    public function delete(Request $req)
    {
        $req->validate([
            'id' => 'required|exists:leagues,id,deleted_at,NULL'
        ]);
        $result = League::find($req->id)->delete();
        if ($result) {
            return response()->success(__('league')['delete_success']);
        }
        return response()->error(__('league')['delete_failure']);
    }
}
