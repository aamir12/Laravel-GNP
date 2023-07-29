<?php

namespace App\Classes;

use App\Classes\UserManager;
use App\Mail\InvitedToLeague;
use App\Models\EmailConfig;
use App\Models\Group;
use App\Models\League;
use App\Models\LeagueEntrant;
use App\Models\LeagueInvite;
use App\Models\Meta;
use App\Models\User;
use App\Services\StorageService;
use Auth;

class LeagueManager
{
    public static function create($req)
    {
        $leagueData = $req->all();
        if (isset($req->parent_id) && $req->parent_id > 0) {
            $data = self::checkParentId($req->parent_id);
            if ($data == 'Public' || $data == 'Private') {
                $leagueData['type'] = $data;
            } else {
                return $data;
            }
        }
        if (!isset($req->score_aggregation_period)) {
            $leagueData['score_aggregation_period'] = config('kpi.base_period');
        }
        $leagueData['owner_id'] = 0;
        if ($req->hasFile('image')) {
            $leagueData = self::storeImage($req, $leagueData);
        }
        $league = League::create($leagueData);
        self::setUsersAndMetaData($req, $league);
        if ($league) {
            return response()->success(__('league')['created_success'], $league->getAttributes());
        }
    }

    public static function checkParentId($id)
    {
        $league = League::firstWhere([
            'id' => $id,
            'parent_id' => 0,
            'type' => 'Public'
        ]);

        if ($league) {
            $leagueEntrant = LeagueEntrant::where('league_id', $id)->count();
            if ($leagueEntrant > 0) {
                return response()->error(__('league')['parent_error']);
            }
            return $league->type;
        }
        return response()->error(__('league')['parent_error']);
    }

    public static function update($req)
    {
        $leagueData = $req->all();
        if (isset($req->parent_id) && $req->parent_id > 0) {
            $data = self::checkparentid($req->parent_id);
            if ($data == 'Public' || $data == 'Private') {
                $leagueData['type'] = $data;
            } else {
                return $data;
            }
        }
        $leagueData['owner_id'] = 0;
        if ($req->hasFile('image')) {
            $leagueData = self::storeImage($req, $leagueData);
        }
        $oldLeague = League::find($req->id);
        $oldLeague->fill($leagueData);
        if ($oldLeague->isClean()) {
            return response()->success(__('nothing_updated'), $oldLeague);
        }
        $league = League::updateData($req->id, $leagueData);
        $result = self::setUsersAndMetaData($req, $league, 'update');
        if ($result != 'success') {
            return $result;
        }
        if ($league) {
            return response()->success(__('league')['update_success'], $league->getAttributes());
        }
    }

    public static function listLeagues()
    {
        return League::with('children', 'entrants')->where([
            'type' => 'Public',
            'parent_id' => 0
        ])->get();
    }

    public static function createPrivateLeague($req)
    {
        $user = Auth::user();
        $leagueData = $req->all();
        $leagueData['type'] = 'Private';
        $leagueData['parent_id'] = 0;

        $leagueData['owner_id'] = $user->id;
        if ($req->hasFile('image')) {
            $leagueData = self::storeImage($req, $leagueData);
        }

        $league = League::create($leagueData);
        $league->entrants()->attach($user->id);
        self::setUsersAndMetaData($req, $league);
        if ($league) {
            return response()->success(__('league')['created_success'], $league->getAttributes());
        }
    }

    public static function inviteUserToLeague($req)
    {
        $invitee = User::find($req->user_id);

        if (! $invitee->hasRole('user')) {
            return 'erroruserrole';
        } else if ($req->user_id == Auth::id()) {
            return 'ownerandusersame';
        }

        $league = LeagueInvite::create([
            'league_owner_id' => Auth::id(),
            'invitee_id' => $invitee->id,
            'league_id' => $req->league_id
        ]);

        if (EmailConfig::findByType('league_invite')->is_enabled == 1) {
            Mail::to($invitee)->send(new InvitedToLeague($invitee, Auth::user()));
        }
        return $league;
    }

    public static function updatePrivateLeague($req)
    {
        $league = League::find($req->id);

        if (!$league) {
            return response()->error(__('league')['permission_denied']);
        }

        $leagueData = $req->all();
        $leagueData['type'] = 'Private';
        $leagueData['parent_id'] = 0;

        if ($req->hasFile('image')) {
            $leagueData = self::storeImage($req, $leagueData, $league);
        }

        $league = League::updateData($req->id, $leagueData);
        $result = self::setUsersAndMetaData($req, $league, 'update');

        if ($result != 'success') {
            return $result;
        }

        if ($league) {
            return response()->success(__('league')['update_success'], $league->getAttributes());
        }
    }

    public static function acceptLeagueInvite($leagueId)
    {
        $league = LeagueInvite::firstWhere([
            'invitee_id' => Auth::user()->id,
            'league_id' => $leagueId
        ]);

        if (LeagueInvite::updateData($league, ['accepted' => 1]) != 'leagueinvitenotfound') {
            return true;
        }
        return false;
    }

    public static function getUserLeague($id)
    {
        return League::with('meta', 'entrants')->where('id', $id)->where(function ($q) {
            $leagueEntrant = function ($query) {
                $query->where('user_id', Auth::user()->id);
            };
            $q->whereHas('entrants', $leagueEntrant)->orWhere('owner_id', Auth::user()->id);
        })->first();
    }

    public static function listUserLeagues()
    {
        return League::with('children', 'meta', 'entrants')->where(function ($q) {
            $leagueEntrant = function ($query) {
                $query->where('user_id', Auth::user()->id);
            };
            $q->whereHas('entrants', $leagueEntrant)->orWhere('owner_id', Auth::user()->id);
        })->get();
    }

    public static function rejectLeagueInvite($leagueId)
    {
        $league = LeagueInvite::firstWhere([
            'invitee_id' => Auth::user()->id,
            'league_id' => $leagueId
        ]);

        if (LeagueInvite::updateData($league, ['rejected' => 1]) != 'leagueinvitenotfound') {
            return true;
        }
        return false;
    }

    public static function shutDownPrivateLeague($leagueId)
    {
        return League::where([
            'owner_id' => Auth::user()->id,
            'id' => $leagueId
        ])->delete();
    }

    public static function leaveLeague($req)
    {
        $user = Auth::user();
        $league = League::find($req->id);
        if ($league->type == 'Public') {
            return response()->error(__('league')['leave_public_failure']);
        }
        if ($league->owner_id == $user->id) {
            return response()->error(__('league')['leave_owner_failure']);
        }

        if ($league->group->containsUser($user)) {
            return response()->error(__('league')['leave_group_failure']);
        }

        return LeagueEntrant::where([
            'league_id' => $req->id,
            'user_id' => $user->id
        ])->delete();
    }

    public static function storeImage($req, $data, $league = null)
    {
        $data = array_merge($data, StorageService::storeImage($req->file('image')));
        $req->request->remove('image');
        return $data;
    }

    public static function setUsersAndMetaData($req, $league, $type = 'create')
    {
        if (isset($req->group_id) && $req->group_id > 0) {
            if ($type == 'create' || !League::firstWhere(['parent_id' => $req->id])) {
                $group = Group::find($req->group_id);
                $userIds = $group->getAllUsersInSubgroups()->modelKeys();
                foreach ($userIds as $index => $item) {
                    if (isset($item)) {
                        $league->entrants()->syncWithoutDetaching($item);
                    }
                }
            } else {
                return response()->error(__('league')['user_add_error']);
            }
        }

        $league->setMetadata(Meta::extractMetadata($req->all()));

        return 'success';
    }
}
