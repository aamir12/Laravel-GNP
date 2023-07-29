<?php

namespace App\Http\Controllers\Auth;

use App\Classes\PassportHelper;
use App\Classes\UserManager;
use App\Classes\VerifyUserManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Authentication
 *
 * APIs relating to authentication.
 */
class AuthController extends Controller
{
    /**
     * Register
     *
     * @unauthenticated
     *
     * @bodyParam email string required Max: 191. User Email Example: gevokihe@mail-guru.net
     * @bodyParam paypal_email string Max: 191. User Paypal Email Example: gevokihe@mail-guru.net
     * @bodyParam username string Max: 100. User Unique UserName Example: john
     * @bodyParam password string required Max: 191. User Password Example: John@12345
     * @bodyParam first_name string Max: 100. User First Name Example: John
     * @bodyParam last_name string Max: 100. User Last Name Example: Chiswell
     * @bodyParam activation_code string Max: 255. User Activation Code Example: JC0005
     * @bodyParam dob date Date of birth formatted as YYYY-mm-dd Example: 2019-07-22
     * @bodyParam phone string Max: 20. User phone no. Example:+917778885555
     * @bodyParam metadata json  User Meta data Example: [{"title":"john"}, {"url" : "https://temp-mail.org/en/"}]
     * @bodyParam timezone string Max: 100 User timezone Example: Europe/London
     *
     * @responseFile 200 resources/responses/Auth/Auth/register.json
     * @responseFile 422 resources/responses/Auth/Auth/register-422.json
     */
    public function register(RegisterRequest $req)
    {
        return response()->success(__('auth')['register_success'], UserManager::register($req->validated()));
    }

    /**
     * Login
     *
     * @unauthenticated
     *
     * @bodyParam username string required Max: 100. Username or Email is required Example: gevokihe
     * @bodyParam password string required Max: 191. User Password Example: John@12345
     *
     * @responseFile 200 resources/responses/Auth/Auth/login.json
     * @responseFile 422 resources/responses/Auth/Auth/login-422.json
     */
    public function login(Request $req)
    {
        $credentials = $req->validate(['username' => 'required', 'password' => 'required']);
        $response = UserManager::login($credentials);

        if ($response === 'password_expired') {
            return response()->error(__('auth')['password_expired']);
        }
        if ($response === 'invalid_credentials') {
            return response()->error(__('auth')['invalid_credentials'], null, 401);
        }
        return response()->success(__('auth')['login_success'], $response);
    }

    /**
     * Refresh Token
     *
     * @bodyParam  refresh_token string required Provide refresh token Example: def50200c5ec82fd94f4548f563b40d49ea51f9a587d7008f77909d3d9c98620c2bb08d7c37eefa6e25f96279fc6ee7802575d2155a4c552bc67098b9e762a34e269ae303d498e32d1671e12e105e77e6ea756154a5e7070eb89bcd57437d564d12f7bdf553f275b2702c629b5e78bdbe025f64596362d25bc8e21eeba120a5aadfa0f0829f905a469531828b860c9c3d475db0f9f02255ae50115b9476c852a6c99c4c0162dd9cdf8b2dab8c7c522b352bb52c8f9bffbd2b9061058205ea82c1fba1068b83b67e742757bf9294f0515db429cd5becee4cbafd799ae5d9c7f771ad58cf166e998d54b7bcae33ecd5b35afa68b95739876b09ac6ae3fc34e097351f3ebb0be65c61eb3855317f51e7f7704718cc57608b11d717ae8d57b43f0d4e19bc520765001ae2d87347b3580b0ec8930a8c910f9e767ab3cf0adddcc5a0f8393687fb636ec0ec3cbaa9662ba61dc623d073a8e517741f607066f3ebd4fd025478
     *
     * @responseFile 200 resources/responses/Auth/Auth/refresh-token.json
     * @responseFile 422 resources/responses/Auth/Auth/refresh-token-422.json
     */
    public function refreshToken(Request $req)
    {
        $req->validate(['refresh_token' => 'required']);
        $passportResponse = PassportHelper::refreshToken($req['refresh_token']);

        if (isset($passportResponse['error'])) {
            return response()->error(__('auth')['invalid_refresh_token'], null, 401);
        }

        $response['auth'] = $passportResponse;
        return response()->success(__('auth')['token_refresh_success'], $response);
    }

    /**
     * Change Password
     *
     * @bodyParam password string required Password rules are determined by the app's configuration. Example: Test@12345
     * @bodyParam cpassword string required Must match the value of `password`. Example: Test@12345
     *
     * @responseFile 200 resources/responses/Auth/Auth/change-password.json
     * @responseFile 422 resources/responses/Auth/Auth/change-password-422.json
     */
    public function changePassword(ChangePasswordRequest $req)
    {
        $data = $req->validated();
        Auth::user()->password = bcrypt($data['password']);

        return Auth::user()->save() ?
                response()->success(__('auth')['password_change_success']) :
                response()->error(__('auth')['password_change_failed']);
    }

    /**
     * Logout
     *
     * @responseFile 200 resources/responses/Auth/Auth/log-out.json
     */
    public function logout()
    {
        UserManager::logout();
        return response()->success(__('auth')['logout_success']);
    }

    /**
     * Reset Password
     *
     * @unauthenticated
     *
     * @bodyParam email string required Max: 191. User Email Example: gevokihe@mail-guru.net
     */
    public function requestPasswordReset(Request $req)
    {
        $req->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        UserManager::requestPasswordReset($req->email);

        return response()->success(__('auth')['reset_password_mail_success']);
        // return response()->error(__('auth')['reset_password_mail_failure']);
    }

    /**
     * Get Logged In User
     *
     * @responseFile 200 resources/responses/Auth/Auth/get-login-user.json
     */
    public function loggedInUser(Request $req)
    {
        return response()->success('User Record!', json_decode((string) $req->user(), true));
    }

    /**
     * Verify User
     *
     * @unauthenticated
     *
     * Verifies the user via a unique token.
     */
    public function verifyUser($token)
    {
        $response = VerifyUserManager::verifyToken($token);

        if (__('verify_email')['success'] == $response) {
            return response()->success($response);
        }

        if (__('verify_email')['already_exist'] == $response) {
            return response()->error($response);
        }

        if (__('verify_email')['link_expire'] == $response) {
            return response()->error($response);
        }
    }
}
