<?php

namespace App\Http\Controllers\User;

use App\Actions\ClaimPrizeAction;
use App\Http\Controllers\Controller;
use App\Models\Prize;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group User API - Prizes
 *
 * User APIs for interacting with prizes.
 *
 * Before a user can actually gain access to a prize they've won (whether it be
 * physical, digital or cash), they must first claim it.
 */
class PrizeController extends Controller
{
    /**
     * Claim Prize
     *
     * Marks the specified prize as having been claimed by the currently authenticated user.
     *
     * @bodyParam id int required The ID of the prize being claimed. Example: 1
     *
     * @responseFile 200 resources/responses/User/Prize/claim-prize.json
     */
    public function claim(Request $req, ClaimPrizeAction $action)
    {
        $req->validate(['id' => 'required|exists:prizes']);

        try {
            $action->execute(Auth::user(), Prize::find($req->id));
            return response()->success(__('prize')['success_claim']);
        } catch (Exception $ex) {
            return response()->error($ex->getMessage());
        }
    }
}
