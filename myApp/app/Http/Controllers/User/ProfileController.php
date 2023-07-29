<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @group User API - Profile Management
 *
 * User APIs for managing user profile.
 */
class ProfileController extends Controller
{
    /**
     * Update Profile
     *
     * Updates the user profile with the values provided.
     *
     * @bodyParam email string Max: 191. Valid Email only Example: johndoe@gmail.com
     * @bodyParam username string Max: 100. Can be blank or User Unique UserName only Example: JohnDoe
     * @bodyParam first_name string Max: 100. Can be blank or Alphabets only Example: John
     * @bodyParam last_name string Max: 100. Can be blank or Alphabets only Example: Doe
     * @bodyParam paypal_email String Max: 191. Can be blank or Valid Email only Example: gevokihe@mail-guru.net
     * @bodyParam phone string Max: 20. Can be blank or Valid Contact Number starting with + only Example: +919090909090
     * @bodyParam timezone string Max: 100. Can be blank or valid timezone only Example: Europe/London
     * @bodyParam dob string Can be blank or valid date only Example: 2000-09-01
     *
     * @responseFile 200 resources/responses/User/Profile/update.json
     * @responseFile 422 resources/responses/User/Profile/update-422.json
    */
    public function update(UpdateProfileRequest $req)
    {
        $user = Auth::user();
        $user->fill($req->validated());
        $user->save();

        return $user->wasChanged()
            ? response()->success(__('user')['profile_update_success'], $user)
            : response()->success(__('nothing_updated'), $user);
    }
}
