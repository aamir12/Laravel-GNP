<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prize\BulkCreatePrizeRequest;
use App\Http\Requests\Prize\CreatePrizeRequest;
use App\Http\Requests\Prize\UpdatePrizeRequest;
use App\Models\Prize;
use App\Services\PrizeService;
use Illuminate\Http\Request;

/**
 * @group Admin API - Prizes
 *
 * Admin APIs for managing prizes.
 *
 * Prizes represent the link between competitions and stock. Any number of
 * prizes can be assigned to a given competition but each prize corresponds to
 * one particular stock item.
 *
 * A winner count can be specified on a prize in order to limit the number of
 * potential winners. In the case of physical goods, the winner count cannot
 * exceed the quantity in stock.
 */
class AdminPrizeController extends Controller
{
    /**
     * Create Prize
     *
     * Creates a new prize using the values provided.
     *
     * @bodyParam name string required Max: 191. The name of the prize. Example: iPhone 12 Pro
     * @bodyParam competition_id int required The ID of the competition the prize is for. Example: 1
     * @bodyParam image file A display image for the prize. No-example
     * @bodyParam type string required The type of prize. Must be one of: cash/goods/digital. Example: cash
     * @bodyParam amount float The cash amount of the prize. Required only if the `type` is "cash". Example: 10.00
     * @bodyParam currency string Max: 191. The currency of the prize. Required only if the `type` is "cash". Example: GBP
     * @bodyParam reference string Max: 191. A reference for the prize.
     * @bodyParam max_winners int required The number of possible winners of the prize. Example: 2
     *
     * @responseFile 200 resources/responses/Admin/Prize/create.json
     */
    public function create(CreatePrizeRequest $request)
    {
        $prize = PrizeService::createPrize($request->validated());
        return response()->success(__('prize')['created_success'], $prize);
    }

    /**
     * Bulk Create Prizes
     *
     * Creates one or more prizes using the values provided in an array.
     *
     * @bodyParam prizes[0][name] string required Max: 191. The name of the prize. Example: iPhone 12 Pro
     * @bodyParam prizes[0][competition_id] int required The ID of the competition the prize is for. Example: 1
     * @bodyParam prizes[0][image] file A display image for the prize. No-example
     * @bodyParam prizes[0][type] string required The type of prize. Must be one of: cash/goods/digital. Example: cash
     * @bodyParam prizes[0][amount] float The cash amount of the prize. Required only if the `type` is "cash". Example: 10.00
     * @bodyParam prizes[0][currency] string Max: 191. The currency of the prize. Required only if the `type` is "cash". Example: GBP
     * @bodyParam prizes[0][reference] string Max: 191. A reference for the prize.
     * @bodyParam prizes[0][max_winners] int required The number of possible winners of the prize. Example: 2
     *
     * @responseFile 200 resources/responses/Admin/Prize/bulk-create.json
     */
    public function bulkCreate(BulkCreatePrizeRequest $request)
    {
        $prizes = PrizeService::createPrizes($request->validated());
        return response()->success(__('prize')['created_success'], $prizes);
    }

    /**
     * List Prizes
     *
     * Lists all prizes.
     *
     * @responseFile 200 resources/responses/Admin/Prize/list.json
     */
    public function list()
    {
        $prizes = Prize::with('stock')->get();
        return response()->success(__('prize')['list_success'], $prizes);
    }


    /**
     * Get Prize
     *
     * Retrieves the prize specified by `id`.
     *
     * @bodyParam id int required The ID of the prize.
     *
     * @responseFile 200 resources/responses/Admin/Prize/get.json
     */
    public function get(Request $req)
    {
        $req->validate(['id' => 'required|exists:prizes,id,deleted_at,NULL']);
        $prize = Prize::with('stock')->find($req->id);
        return response()->success(__('prize')['list_success'], $prize);
    }

    /**
     * Update Prize
     *
     * Updates the specified prize with the values provided.
     *
     * @bodyParam id string required The ID of the prize being updated. Example: 1
     * @bodyParam name string Max: 191. The name of the prize. Example: iPhone 12 Pro
     * @bodyParam competition_id int The ID of the competition the prize is for. Example: 1
     * @bodyParam image file A display image for the prize. No-example
     * @bodyParam type string required The type of prize. Must be one of: cash/goods/digital. Example: cash
     * @bodyParam amount float The cash amount of the prize. Required only if the `type` is "cash". Example: 10.00
     * @bodyParam currency string Max: 191. The currency of the prize. Required only if the `type` is "cash". Example: GBP
     * @bodyParam reference string Max: 191. A reference for the prize.
     * @bodyParam max_winners int required The number of possible winners of the prize. Example: 2
     *
     * @responseFile 200 resources/responses/Admin/Prize/update.json
     */
    public function update(UpdatePrizeRequest $request)
    {
        $prize = PrizeService::updatePrize(Prize::find($request->id), $request->validated());

        if ($prize->wasChanged()) {
            return response()->success(__('prize')['update_success'], $prize);
        }
        return response()->success(__('nothing_updated'));
    }

    /**
     * Delete Prize
     *
     * Deletes the prize specified by `id`.
     *
     * @bodyParam id int required The ID of the prize being deleted. Example: 1
     *
     * @responseFile 200 resources/responses/Admin/Prize/delete.json
     */
    public function delete(Request $req)
    {
        $req->validate(['id' => 'required|exists:prizes,id,deleted_at,NULL']);
        Prize::find($req->id)->delete();
        return response()->success(__('prize')['delete_success']);
    }
}
