<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Achievement\CreateAchievementRequest;
use App\Http\Requests\Achievement\UpdateAchievementRequest;
use App\Models\Achievement;
use App\Services\StorageService;
use Illuminate\Http\Request;

/**
 * @group Admin API - Achievements
 *
 * Admin APIs for managing achievements.
 *
 * Achievements are similar to prizes but instead of being gained through
 * competitions are awarded for reaching specific goals.
 */
class AdminAchievementController extends Controller
{
    /**
     * Create Achievement
     *
     * Creates a new achievement using the values provided.
     *
     * @bodyParam stock_id int The ID of the stock to be used for the achievement. Example: 1
     * @bodyParam name string required Max: 191. The name of the achievement. Example: Top 10
     * @bodyParam description string required A description for the achievement. Example: You reached the Top 10!
     * @bodyParam image file A display image for the achievement. No-example
     *
     * @responseFile 200 resources/responses/Admin/Achievement/create.json
     */
    public function create(CreateAchievementRequest $req)
    {
        $data = $req->validated();

        if (isset($data['image'])) {
            $data = array_merge($data, StorageService::storeImage($data['image']));
        }

        $achievement = Achievement::create($data);
        return response()->success(__('achievement')['create_success'], $achievement);
    }

    /**
     * Get Achievement
     *
     * Retrieves the achievement specified by `id`.
     *
     * @bodyParam id int required The ID of the achievement. Example: 1
     *
     * @responseFile 200 resources/responses/Admin/Achievement/get.json
     */
    public function get(Request $req)
    {
        $req->validate(['id' => 'required|exists:achievements,id,deleted_at,NULL']);
        return response()->success(__('achievement')['found_success'], Achievement::find($req->id));
    }

    /**
     * List Achievements
     *
     * Lists all achievements.
     *
     * @responseFile 200 resources/responses/Admin/Achievement/list.json
     */
    public function list()
    {
        return response()->success(__('achievement')['list_success'], Achievement::get());
    }

    /**
     * Update Achievement
     *
     * Updates the specified achievement with the values provided.
     *
     * @bodyParam id int required The ID of the achievement being updated. Example: 1
     * @bodyParam stock_id int The ID of the stock to be used for the achievement. Example: 1
     * @bodyParam name string required Max: 191. The name of the achievement. Example: Top 10
     * @bodyParam description string required A description for the achievement. Example: You reached the top 10 users!
     * @bodyParam image file A display image for the achievement. No-example.
     *
     * @responseFile 200 resources/responses/Admin/Achievement/update.json
     */
    public function update(UpdateAchievementRequest $req)
    {
        $data = $req->validated();

        if (isset($data['image'])) {
            $data = array_merge($data, StorageService::storeImage($data['image']));
        }

        $achievement = Achievement::find($req->id);
        $achievement->fill($data);
        $achievement->save();

        return response()->success(__('achievement')['update_success'], $achievement);
    }

    /**
     * Delete Achievement
     *
     * Deletes the achievement specified by `id`.
     *
     * @bodyParam id int required The ID of the achievement. Example: 1
     *
     * @responseFile 200 resources/responses/Admin/Achievement/delete.json
     */
    public function delete(Request $req)
    {
        $req->validate(['id' => 'required|exists:achievements,id,deleted_at,NULL']);
        Achievement::find($req->id)->delete();
        return response()->success(__('achievement')['delete_success']);
    }
}
