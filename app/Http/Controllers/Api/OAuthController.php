<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class OAuthController extends Controller
{
    /**
     * Issue OAuth 2.0 Bearer Access Token using Static Credentials
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function issueToken(Request $request)
    {
        $grantType = $request->input('grant_type');
        $clientId = $request->input('client_id');
        $clientSecret = $request->input('client_secret');

        if ($grantType !== 'client_credentials') {
            return response()->json([
                'error' => 'unsupported_grant_type',
                'error_description' => 'The grant type must be client_credentials.'
            ], 400);
        }

        if (empty($clientId) || empty($clientSecret)) {
            return response()->json([
                'error' => 'invalid_request',
                'error_description' => 'Client ID and Client Secret are required.'
            ], 400);
        }

        // Validate against static clients defined in config/oauth.php
        $staticClients = config('oauth.clients', []);
        $matchedClient = null;

        foreach ($staticClients as $client) {
            if (
                hash_equals($client['client_id'], $clientId) &&
                hash_equals($client['client_secret'], $clientSecret)
            ) {
                $matchedClient = $client;
                break;
            }
        }

        if (!$matchedClient) {
            return response()->json([
                'error' => 'invalid_client',
                'error_description' => 'Client authentication failed. Invalid client_id or client_secret.'
            ], 401);
        }

        // Generate signed JWT Bearer Token
        $secretKey = config('oauth.jwt_secret');
        $ttl = (int) config('oauth.token_ttl', 3600);
        $currentTime = time();

        $payload = [
            'iss' => config('app.url', 'http://localhost'),
            'sub' => $clientId,
            'aud' => 'powerbi-api',
            'iat' => $currentTime,
            'nbf' => $currentTime,
            'exp' => $currentTime + $ttl,
            'client_name' => $matchedClient['name'] ?? 'Static Client'
        ];

        $jwtToken = JWT::encode($payload, $secretKey, 'HS256');

        return response()->json([
            'access_token' => $jwtToken,
            'token_type' => 'Bearer',
            'expires_in' => $ttl
        ]);
    }
}
