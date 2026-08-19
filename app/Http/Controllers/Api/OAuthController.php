<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;

class OAuthController extends Controller
{
    /**
     * Issue OAuth 2.0 Bearer Access Token using Static Credentials
     * Supports POST & GET requests for Power BI Web.Contents compatibility.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function issueToken(Request $request)
    {
        try {
            $grantType = (string) ($request->input('grant_type') ?: $request->query('grant_type') ?: 'client_credentials');
            $clientId = (string) ($request->input('client_id') ?: $request->query('client_id') ?: env('OAUTH_CLIENT_ID', 'powerbi_client_2026'));
            $clientSecret = (string) ($request->input('client_secret') ?: $request->query('client_secret') ?: env('OAUTH_CLIENT_SECRET', 'sec_erp_api_9823472398472938'));

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

            // 3. Generate signed JWT Bearer Token using native PHP (zero vendor dependency)
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

            $jwtToken = trim(preg_replace('/\s+/', '', $this->generateJwt($payload, $secretKey)));

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

    /**
     * Generate a signed HS256 JWT using native PHP functions.
     */
    private function generateJwt(array $payload, string $secret): string
    {
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
