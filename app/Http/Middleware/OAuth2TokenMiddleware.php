<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OAuth2TokenMiddleware
{
    /**
     * Handle an incoming request with native PHP JWT & API key verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');
        $apiKey = $request->header('X-API-KEY') ?: $request->header('X-SECRET-KEY');

        // 1. Direct Secret API Key Header Support
        if (!empty($apiKey)) {
            $allowedSecrets = [
                config('oauth.default_client.client_secret'),
                env('OAUTH_CLIENT_SECRET'),
                env('POWERBI_API_KEY'),
            ];

            foreach ($allowedSecrets as $secret) {
                if (!empty($secret) && hash_equals($secret, $apiKey)) {
                    return $next($request);
                }
            }
        }

        // 2. Standard OAuth 2.0 Bearer JWT Token Support
        if (empty($authHeader) || !preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'Missing or malformed Authorization header. Provide Bearer token or X-API-KEY header.'
            ], 401);
        }

        $jwtToken = $matches[1];
        $secretKey = (string) (config('oauth.jwt_secret') ?: env('OAUTH_JWT_SECRET', 'super_secret_jwt_key_for_erp_api_2026'));

        try {
            $decodedPayload = $this->verifyJwt($jwtToken, $secretKey);
            $request->attributes->set('oauth_client', $decodedPayload);

            return $next($request);
        } catch (Exception $e) {
            Log::warning('OAuth 2.0 Bearer Token Validation Failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'invalid_token',
                'message' => 'The access token is invalid, expired, or tampered with.'
            ], 401);
        }
    }

    /**
     * Verify HS256 JWT signature and expiration using native PHP.
     */
    private function verifyJwt(string $jwt, string $secret): object
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new Exception("Malformed JWT token structure.");
        }

        list($base64Header, $base64Payload, $base64Signature) = $parts;

        // Verify signature
        $expectedSignature = rtrim(strtr(base64_encode(hash_hmac('sha256', $base64Header . "." . $base64Payload, $secret, true)), '+/', '-_'), '=');
        if (!hash_equals($expectedSignature, $base64Signature)) {
            throw new Exception("Invalid JWT token signature.");
        }

        // Decode payload
        $payload = json_decode(base64_decode(strtr($base64Payload, '-_', '+/')));
        if (!$payload) {
            throw new Exception("Invalid JWT payload.");
        }

        // Check expiration
        if (isset($payload->exp) && time() >= $payload->exp) {
            throw new Exception("JWT token has expired.");
        }

        return $payload;
    }
}
