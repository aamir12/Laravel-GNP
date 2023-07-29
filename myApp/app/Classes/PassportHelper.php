<?php

namespace App\Classes;

use App\Models\OauthClient;
use Illuminate\Support\Facades\Http;

class PassportHelper
{
    public static function login($credentials)
    {
        $client = OauthClient::firstWhere('password_client', 1);
        $data = [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'scope' => '*',
        ];
        return Http::post(config('app.service_name') . '/oauth/token', $data)->json();
    }

    public static function refreshToken($token)
    {
        $client = OauthClient::firstWhere('password_client', 1);
        $data = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $token,
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'scope' => '',
        ];
        return Http::post(config('app.service_name') . '/oauth/token', $data)->json();
    }
}