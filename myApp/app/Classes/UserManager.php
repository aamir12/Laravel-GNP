<?php

namespace App\Classes;

use App\Classes\PassportHelper;
use App\Events\UserRegistered;
use App\Mail\PasswordReset as PasswordResetMail;
use App\Mail\VerifyEmail;
use App\Models\Meta;
use App\Models\PasswordReset;
use App\Models\PasswordSecurity;
use App\Models\Role;
use App\Models\User;
use App\Models\VerifyUser;
use App\Models\Competition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class UserManager
{
    public static function register($data)
    {
        if (isset($data['activation_code'])) {
            $user = User::firstWhere('activation_code', $data['activation_code']);
        } else if (isset($data['external_id'])) {
            $user = User::firstWhere('external_id', $data['external_id']);
        } else {
            $user = new User;
        }

        $data['is_activated'] = 1;
        $data['password'] = bcrypt($data['password']);
        $user->fill($data);
        $user->save();
        $user->roles()->sync([Role::firstWhere('name', 'user')->id]);
        $user->setMetadata(Meta::extractMetadata($data));

        PasswordSecurity::create([
            'user_id' => $user->id,
            'password_expiry_days' => config('auth.password_expiry_days'),
            'password_updated_at' => now(),
        ]);

        UserRegistered::dispatch($user);
        $credentials = ['username' => $data['email'], 'password' => $data['password']];
        return PassportHelper::login($credentials);
    }

    public static function verifyUser($user, $isSendEmail)
    {
        $verifyUser = VerifyUser::updateOrCreate(
            ['user_id' => $user->id],
            ['user_id' => $user->id, 'token' => str_random(40)]
        );
        if ($isSendEmail == true) {
            Mail::to($user)->send(new VerifyEmail($user));
        }
        if (isset($user->activation_code) && $user->activation_code != '') {
            return VerifyUser::where('id', $verifyUser->id)->update(['verified' => 1]);
        }
    }

    public static function login($credentials)
    {
        $passportResponse = PassportHelper::login($credentials);

        if (!isset($passportResponse['access_token'])) {
            return 'invalid_credentials';
        }

        $user = User::where('username', $credentials['username'])
            ->orWhere('email', $credentials['username'])
            ->first();

        if ($user->isPasswordExpired()) {
            return 'password_expired';
        }

        $responseData['user'] = $user;
        $responseData['auth'] = $passportResponse;
        return $responseData;
    }

    public static function logout()
    {
        $tokenId = Auth::user()->token()->id;
        $tokenRepository = app(TokenRepository::class);
        $refreshTokenRepository = app(RefreshTokenRepository::class);
        $tokenRepository->revokeAccessToken($tokenId);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);
    }

    public static function requestPasswordReset($email)
    {
        $user = User::firstWhere('email', $email);
        $token = str_replace('.', '', str_replace('/', '', bcrypt(time() . rand(000, 999))));

        PasswordReset::create([
            'email' => $email,
            'token' => $token,
            'created_at' => now()
        ]);
        Mail::to($user)->send(new PasswordResetMail($user, $token));
    }
}
