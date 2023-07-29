<?php

namespace App\Http\Middleware;

use App\Models\OauthClient;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Exceptions\MissingScopeException;
use Laravel\Passport\Http\Middleware\CheckCredentials;

class SecureCheckClientCredentials extends CheckCredentials
{
    /**
     * Validate token credentials.
     *
     * @param  \Laravel\Passport\Token  $token
     * @param  \Illuminate\Http\Request  $token
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function validateCredentials($token)
    {
        if (! $token) {
            throw new AuthenticationException;
        }

        $client = OauthClient::findOrFail($token->client_id);

        if ($client->password_client || $client->personal_access_client) {
            throw new AuthorizationException;
        }
    }

    /**
     * Validate token credentials.
     *
     * @param  \Laravel\Passport\Token  $token
     * @param  array  $scopes
     * @return void
     *
     * @throws \Laravel\Passport\Exceptions\MissingScopeException
     */
    protected function validateScopes($token, $scopes)
    {
        if (in_array('*', $token->scopes)) {
            return;
        }

        foreach ($scopes as $scope) {
            if ($token->cant($scope)) {
                throw new MissingScopeException($scope);
            }
        }
    }
}
