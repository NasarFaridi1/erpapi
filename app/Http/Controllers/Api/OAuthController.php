<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Throwable;

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
        try {
            $grantType = (string) $request->input('grant_type');
            $clientId = (string) $request->input('client_id');
            $clientSecret = (string) $request->input('client_secret');

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

            // 1. Check configured static clients in config/oauth.php
            $staticClients = config('oauth.clients', []);
            $matchedClient = null;

            if (is_array($staticClients)) {
                foreach ($staticClients as $client) {
                    $knownId = (string) ($client['client_id'] ?? '');
                    $knownSecret = (string) ($client['client_secret'] ?? '');

                    if (!empty($knownId) && !empty($knownSecret)) {
                        if (hash_equals($knownId, $clientId) && hash_equals($knownSecret, $clientSecret)) {
                            $matchedClient = $client;
                            break;
                        }
                    }
                }
            }

            // 2. Direct env fallback matching default credentials
            if (!$matchedClient) {
                $envId = (string) env('OAUTH_CLIENT_ID', 'powerbi_client_2026');
                $envSecret = (string) env('OAUTH_CLIENT_SECRET', 'sec_erp_api_9823472398472938');

                if (hash_equals($envId, $clientId) && hash_equals($envSecret, $clientSecret)) {
                    $matchedClient = [
                        'client_id' => $envId,
                        'client_secret' => $envSecret,
                        'name' => 'Default PowerBI Client'
                    ];
                }
            }

            if (!$matchedClient) {
                return response()->json([
                    'error' => 'invalid_client',
                    'error_description' => 'Client authentication failed. Invalid client_id or client_secret.'
                ], 401);
            }

            // 3. Generate signed JWT Bearer Token
            $secretKey = (string) (config('oauth.jwt_secret') ?: env('OAUTH_JWT_SECRET', 'super_secret_jwt_key_for_erp_api_2026'));
            $ttl = (int) (config('oauth.token_ttl') ?: env('OAUTH_TOKEN_TTL', 3600));
            $currentTime = time();

            $payload = [
                'iss' => config('app.url', 'https://metaerpapi.aideepseek.uk'),
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
        } catch (Throwable $e) {
            return response()->json([
                'error' => 'server_error',
                'message' => 'OAuth token generation failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
