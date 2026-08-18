<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiKeyMiddleware
{
    /**
     * Universal Authentication Middleware:
     * Accepts Static API Keys (X-API-KEY, ApiKey, ?api_key=)
     * AND OAuth 2.0 Bearer JWT Tokens (Authorization: Bearer <token>)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');
        $apiKey = $request->header('X-API-KEY')
            ?: $request->header('ApiKey')
            ?: $request->query('api_key');

        $staticKey = env('POWERBI_API_KEY', env('OAUTH_CLIENT_SECRET', 'sec_erp_api_9823472398472938'));

        // 1. Verify Static API Key Header / Query Parameter
        if (!empty($apiKey) && hash_equals($staticKey, trim($apiKey))) {
            return $next($request);
        }

        // 2. Verify Authorization Header (Static Key or OAuth 2.0 Bearer JWT)
        if (!empty($authHeader)) {
            $rawToken = preg_replace('/^Bearer\s+/i', '', trim($authHeader));

            // Check if Authorization header directly contains static key
            if (hash_equals($staticKey, $rawToken)) {
                return $next($request);
            }

            // Verify if Authorization header contains a valid OAuth 2.0 Bearer JWT Token
            try {
                $jwtSecret = config('oauth.jwt_secret', env('OAUTH_JWT_SECRET', 'super_secret_jwt_key_for_erp_api_2026'));
                $decoded = JWT::decode($rawToken, new Key($jwtSecret, 'HS256'));

                $request->attributes->set('oauth_client', $decoded);
                return $next($request);
            } catch (Exception $e) {
                Log::warning('OAuth Bearer Token Verification Failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized: Invalid or missing API Key / OAuth Bearer Token'
        ], 401);
    }
}