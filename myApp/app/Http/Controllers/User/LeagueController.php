<?php
namespace App\Http\Controllers\User;

use App\Classes\LeagueManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\League\AcceptLeagueInviteRequest;
use App\Http\Requests\League\CreatePrivateLeagueRequest;
use App\Http\Requests\League\InviteUserToLeagueRequest;
use App\Http\Requests\League\RejectLeagueInviteRequest;
use App\Http\Requests\League\ShutDownPrivateLeagueRequest;
use App\Http\Requests\League\UpdatePrivateLeagueRequest;
use Auth;
use Illuminate\Http\Request;


/**
 * @group User API - Leagues
 *
 * User APIs for managing leagues.
 *
 * Users can create and manage their own <strong>private</strong> leagues.
 */
class LeagueController extends Controller
{
    /**
     * Create Private League
     *
     * Creates a new private league using the values provided.
     *
     * @bodyParam name string required Max: 191. The name of the league. Example: Top Performers
     * @bodyParam description string required A description for the league. Example: A league for the best of the best!
     * @bodyParam image file A display image for the league. No-example
     * @bodyParam score_aggregation_period string The period within which scores will be aggregated. Must be one of: daily/weekly/monthly.Example: daily
     * @bodyParam group_id int The ID of a group. If specified, users within that group will automatically be placed in the league being created. Example: 10
     * @bodyParam metadata json Metadata for the league. Example: [{"title":"leagueMeta"}, {"url" : "https://www.example.com"}]
     *
     * @responseFile 200 resources/responses/User/League/create.json
     */
    public function createPrivateLeague(CreatePrivateLeagueRequest $req)
    {
        $data = $req->validated();
        $result = LeagueManager::createPrivateLeague($req);
        return $result;
    }

    /**
     * Update Private League
     *
     * Updates the specified private league with the values provided.
     *
     * @bodyParam id int required The ID of the league being updated. Example: 1
     * @bodyParam name string required Max: 191. The name of the league. Example: Top Performers
     * @bodyParam description string required A description for the league. Example: A league for the best of the best!
     * @bodyParam image file A display image for the league. No-example
     * @bodyParam score_aggregation_period string The period within which scores will be aggregated. Must be one of: daily/weekly/monthly.Example: daily
     * @bodyParam group_id int The ID of a group. If specified, users within that group will automatically be placed in the league being created. Example: 10
     * @bodyParam metadata json Metadata for the league. Example: [{"title":"leagueMeta"}, {"url" : "https://www.example.com"}]
     *
     * @responseFile 200 resources/responses/User/League/update.json
     */
    public function updatePrivateLeague(UpdatePrivateLeagueRequest $req)
    {
        $data = $req->validated();
        $result = LeagueManager::updatePrivateLeague($req);
        return $result;
    }

    /**
     * Accept League Invite
     *
     * Accepts the league invite specified by `id`, entering the currently authenticated user into that league.
     *
     * @bodyParam league_id int required The ID of the league to accept the invite for. Example: 1
     *
     * @responseFile 200 resources/responses/User/League/accept-league-invite.json
     */
    public function acceptLeagueInvite(AcceptLeagueInviteRequest $req)
    {
        $data = $req->validated();
        $result = LeagueManager::acceptLeagueInvite($data['league_id']);
        if ($result) {
            return response()->success(__('league')['accept_invite_success']);
        } else {
            return response()->error(__('league')['accept_invite_error']);
        }
    }

    /**
     * Get League
     *
     * Retrieves the league specified by `id`.
     *
     * @bodyParam id int required The ID of the league. Example: 1
     *
     * @responseFile 200 resources/responses/User/League/get-league.json
     * @responseFile 422 resources/responses/User/League/get-league-422.json
     */
    public function getUserLeague(Request $req)
    {
        $req->validate([
            'id' => 'required|exists:leagues,id,deleted_at,NULL,owner_id,' . Auth::user()->id
        ]);

        $result = LeagueManager::getUserLeague($req->id);
        return response()->success(__('league')['found_success'], $result);
    }

    /**
     * List Leagues
     *
     * Lists all leagues the currently authenticated user is a member of.
     *
     * @responseFile 200 resources/responses/User/League/list-leagues.json
     */
    public function listUserLeagues()
    {
        $result = LeagueManager::listUserLeagues();
        return response()->success(__('league')['list_success'], $result);
    }

    /**
     * Leave League
     *
     * Leaves the specified league, removing the currently authenticated user from it.
     *
     * @bodyParam id int required The id of the league to leave. Example: 1
     *
     * @responseFile 200 resources/responses/User/League/leave-league.json
     * @responseFile 422 resources/responses/User/League/leave-league-422.json
    */
    public function leaveLeague(Request $req)
    {
        $req->validate([
            'id' => 'required|
                     exists:leagues,id,deleted_at,NULL|
                     exists:league_entrants,league_id,user_id,' . Auth::user()->id
        ]);

        if (LeagueManager::leaveLeague($req)) {
            return response()->success(__('league')['leave_success']);
        }
        return response()->error(__('league')['leave_failure']);
    }

    /**
     * Invite to League
     *
     * Invites the specified user to the specified league.
     *
     * @bodyParam user_id int required The ID of the user being invited. Example: 1
     * @bodyParam league_id int required The ID of the league the user is being invited to. Example: 1
     *
     * @responseFile 200 resources/responses/User/League/invite-to-league.json
     */
    public function inviteToLeague(InviteUserToLeagueRequest $req)
    {
        $data = $req->validated();
        $result = LeagueManager::inviteUserToLeague($req);

        if ($result == 'erroruserrole') {
            return response()->error(__('league')['role_user_error']);
        } else if ($result == 'ownerandusersame') {
            return response()->error(__('league')['owner_user_same_error']);
        }
        return response()->success(__('league')['league_invite_success'], $result->getAttributes());
    }

    /**
     * Reject League Invite
     *
     * Rejects the specified league invite.
     *
     * @bodyParam league_id int required The ID of the league to reject the invite for. Example: 1
     * @responseFile 200 resources/responses/User/League/reject-league-invite.json
    */
    public function rejectLeagueInvite(RejectLeagueInviteRequest $req)
    {
        $data = $req->validated();
        $result = LeagueManager::rejectLeagueInvite($data['league_id']);
        if ($result) {
            return response()->success(__('league')['reject_invite_success']);
        }
        return response()->error(__('league')['accept_invite_error']);
    }

    /**
     * Shut Down Private League
     *
     * Shuts down the specified private league.
     *
     * @bodyParam league_id int required The ID of the league being shut down. The league must be private and owned by the currently authenticated user. Example: 1
     *
     * @responseFile 200 resources/responses/User/League/shut-down-private-league.json
     */
    public function shutDownPrivateLeague(ShutDownPrivateLeagueRequest $req)
    {
        $data = $req->validated();
        $result = LeagueManager::shutDownPrivateLeague($data['id']);
        if ($result) {
            return response()->success(__('league')['shutdown_success']);
        }
        return response()->error(__('league')['shutdown_failure']);
    }

}
