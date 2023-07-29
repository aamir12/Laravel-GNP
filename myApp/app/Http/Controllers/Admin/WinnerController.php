<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Winner;
use Illuminate\Http\Request;

/**
 * @group Admin API - Winners
 *
 * Admin APIs for managing winners.
 *
 * A winner represents the link between a user and their prize. For example, if
 * a user wins multiple prizes, they have been a winner multiple times and, as a
 * result, there will be multiple winner records for that user, one for each
 * prize they've won.
 */
class WinnerController extends Controller
{
    /**
     * List Winners
     *
     * Lists all winners along with the associated user and prize info.
     *
     * @queryParam is_claimed bool Filter the result by whether or not the winner has claimed their prize. Example: 1
     *
     * @responseFile 200 resources/responses/Admin/Winners/list.json
     */
    public function list(Request $req)
    {
        $query = Winner::with('user', 'prize');
        $query->when($req->query('is_claimed') !== null, function($query) use ($req) {
            $query->where('is_claimed', $req->query('is_claimed'));
        });

        return response()->success(__('winner')['list_success'], $query->get());
    }
}