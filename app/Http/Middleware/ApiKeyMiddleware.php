<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request with a simple static API key.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Accept API Key from Header (X-API-KEY, ApiKey, Authorization) or Query Parameter (?api_key=)
        $providedKey = $request->header('X-API-KEY')
            ?: $request->header('ApiKey')
            ?: $request->header('Authorization')
            ?: $request->query('api_key');

        if (!empty($providedKey)) {
            // Strip optional "Bearer " prefix if sent in Authorization header
            $providedKey = preg_replace('/^Bearer\s+/i', '', trim($providedKey));
        }

        $validKey = env('POWERBI_API_KEY', env('OAUTH_CLIENT_SECRET', 'sec_erp_api_9823472398472938'));

        if (empty($providedKey) || !hash_equals($validKey, $providedKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid or missing API Key'
            ], 401);
        }

        return $next($request);
    }
}