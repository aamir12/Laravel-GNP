<?php

namespace App\Http\Controllers\User;

use App\Actions\ClaimAchievementAction;
use App\Http\Controllers\Controller;
use App\Models\AchievementWinner;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group User API - Achievements
 *
 * User APIs for interacting with achievements.
 */
class AchievementController extends Controller
{
    /**
     * Claim Achievement
     *
     * Claims the specified achievement.
     *
     * @bodyParam achievement_winner_id int required The ID of the achievement winner. Example: 1
     *
     * @responseFile 200 resources/responses/User/Achievement/claim.json
     */
    public function claim(Request $req, ClaimAchievementAction $action)
    {
        $req->validate([
            'achievement_winner_id' => 'required|exists:achievement_winners,id,deleted_at,NULL',
        ]);

        try {
            $action->execute(Auth::user(), AchievementWinner::find($req->achievement_winner_id));
            return response()->success(__('achievement')['claim_success']);
        } catch (Exception $e) {
            return response()->error($e->getMessage());
        }
    }
}
