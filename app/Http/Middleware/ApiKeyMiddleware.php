<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request with static X-API-KEY header validation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $providedKey = $request->header('X-API-KEY')
            ?: $request->header('ApiKey')
            ?: $request->header('Authorization')
            ?: $request->query('api_key');

        if (!empty($providedKey)) {
            $providedKey = preg_replace('/^Bearer\s+/i', '', trim($providedKey));
        }

        $validKey = env('POWERBI_API_KEY', 'sec_erp_api_9823472398472938');

        if (empty($providedKey) || !hash_equals($validKey, $providedKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid or missing API Key'
            ], 401);
        }

        return $next($request);
    }
}