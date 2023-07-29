<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Deliverable\UpdateDeliverableRequest;
use App\Models\Deliverable;
use Illuminate\Http\Request;

/**
 * @group Admin API - Deliverables
 *
 * Admin APIs for managing deliverables.
 *
 * Deliverables hold information relating to the delivery of a prize/reward to a
 * winner; this includes a flag that shows whether the item has been shipped or
 * not, shipping information and tracking references (if needed). When a user is
 * awarded a Prize/Achievement, a Deliverable will be automatically created and
 * bound to the Prize/Achievement.
 */
class DeliverableController extends Controller
{
    /**
     * List Deliverables
     *
     * @bodyParam is_shipped bool Filters the results by the `is_shipped` field. If this parameter is omitted, all deliverables will be returned regardless of shipping status. Example: 1
     *
     * @responseFile 200 resources/responses/Admin/Deliverable/list.json
     */
    public function list(Request $req)
    {
        $req->validate(['is_shipped' => 'boolean']);

        $query = Deliverable::with(['achievement_winner.user', 'achievement_winner.prize.stock']);
        if (isset($req->is_shipped)) {
            $query = $query->where('is_shipped', $req->is_shipped);
        }
        return response()->success(__('deliverable')['list_success'], $query->get());
    }

    /**
     * Get Deliverable
     *
     * Gets the deliverable specified by `id`.
     *
     * @bodyParam id int required The ID of the deliverable. Example: 1
     *
     * @responseFile 200 resources/responses/Admin/Deliverable/get.json
     */
    public function get(Request $req)
    {
        $req->validate(['id' => 'required|exists:deliverables']);
        return response()->success(__('deliverable')['found_success'], Deliverable::find($req->id));
    }

    /**
     * Update Deliverable
     *
     * Updates the specified deliverable with the values provided.
     *
     * @bodyParam id string required The ID of the deliverable being updated. Example: 1
     * @bodyParam is_shipped bool Whether or not the deliverable is shipped.
     * @bodyParam shipping_name string. The name of the shipping. Example: Awesome deliverable.
     * @bodyParam shipping_number string. The shipping number of the deliverable. Example: 123456
     * @bodyParam shipping_email string. Valid Email only Example: testuser@gmail.com
     * @bodyParam shipping_addressline1 string The address line 1 of deliverable address Example: 37 Sagar Apartment.
     * @bodyParam shipping_addressline2 string The address line 2 of deliverable address Example: Ashoka Garden.
     * @bodyParam shipping_addressline3 string The address line 3 of deliverable address Example: Bhopal,MP.
     * @bodyParam shipping_postcode string The postcode of deliverable Example 462585
     * @bodyParam shipping_county string The county of deliverable.
     * @bodyParam shipping_country string The country of deliverable Example India.
     * @bodyParam shipping_comment string The comment on deliverable Example Test Comment.
     * @bodyParam tracking_ref string The tracking reference of deliverable Example ABC454
     *
     * @responseFile 200 resources/responses/Admin/Deliverable/update.json
     */
    public function update(UpdateDeliverableRequest $req)
    {
        $deliverable = Deliverable::find($req->id);
        $deliverable->fill($req->validated());
        $deliverable->save();

        if ($deliverable->wasChanged()) {
            return response()->success(__('deliverable')['update_success'], $deliverable);
        }
        return response()->success(__('nothing_updated'), $deliverable);
    }
}
