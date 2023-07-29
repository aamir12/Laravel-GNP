<?php

namespace App\Http\Controllers\Admin;

use App\Classes\BrandingManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Branding\UpdateBrandingRequest;
use App\Models\Branding;

/**
 * @group Admin API - Branding
 *
 * Admin APIs for managing branding.
 *
 * The branding used on any web pages or emails served by the system can be
 * customised. Company details, logos, colours etc. can all be configured as
 * needed.
 */
class BrandingController extends Controller
{
    /**
     * Update Branding
     *
     * @bodyParam company_name string Max: 191. The name of the company. Example: Earnie
     * @bodyParam logo file An image of the company's logo. Example: No-example
     * @bodyParam primary_color string Max: 191. Primary colour to be used for branding. This will be used to style email and webpage templates. Example: #d4d4d4
     * @bodyParam company_address string Max: 191. The company's address Example: 742 Evergreen Terrace, Springfield
     * @bodyParam terms_url string Max: 191. URL of the company's terms and conditions page. Example: https://www.example.com/terms
     * @bodyParam privacy_url string Max: 191. URL of the company's privacy page. Example: https://www.example.com/privacy
     * @bodyParam support_email string Max: 191. The company's support email address. Example: example@gmail.com
     *
     * @responseFile 200 resources/responses/Admin/Branding/update.json
     */
    public function update(UpdateBrandingRequest $req)
    {
        $branding = BrandingManager::update($req->validated());
        if ($branding->wasChanged()) {
            return response()->success(__('branding')['update_success'], $branding);
        }
        return response()->success(__('nothing_updated'), $branding);
    }

    /**
     * Get Branding
     *
     * @responseFile 200 resources/responses/Admin/Branding/get.json
     */
    public function get()
    {
        return response()->success(__('branding')['get_success'], Branding::firstOrFail());
    }
}
