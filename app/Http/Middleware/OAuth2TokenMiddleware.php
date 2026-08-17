<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OAuth2TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');
        $apiKey = $request->header('X-API-KEY') ?: $request->header('X-SECRET-KEY');

        // 1. Direct Secret API Key Header Support (for Power BI "From Web" UI dialog)
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
        $secretKey = config('oauth.jwt_secret');

        try {
            // Verify JWT token signature and expiration
            $decoded = JWT::decode($jwtToken, new Key($secretKey, 'HS256'));

            // Store decoded token payload in request for controllers
            $request->attributes->set('oauth_client', $decoded);

            return $next($request);
        } catch (Exception $e) {
            Log::warning('OAuth 2.0 Bearer Token Validation Failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'invalid_token',
                'message' => 'The access token is invalid, expired, or tampered with.'
            ], 401);
        }
    }
}
