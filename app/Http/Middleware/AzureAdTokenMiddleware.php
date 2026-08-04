<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AzureAdTokenMiddleware
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

        // Optional API key fallback for backwards compatibility if X-API-KEY is provided
        $apiKey = $request->header('X-API-KEY');
        if (empty($authHeader) && !empty($apiKey)) {
            $configuredApiKey = env('POWERBI_API_KEY');
            if (!empty($configuredApiKey) && hash_equals($configuredApiKey, $apiKey)) {
                return $next($request);
            }
        }

        if (empty($authHeader) || !preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
                'message' => 'Missing or invalid Authorization header. Expected Bearer token.'
            ], 401);
        }

        $jwtToken = $matches[1];

        try {
            $decodedPayload = $this->verifyToken($jwtToken);

            // Attach claims to the request attributes for downstream controllers
            $request->attributes->set('azure_user', $decodedPayload);

            return $next($request);
        } catch (Exception $e) {
            Log::warning('Microsoft Azure AD Token Verification Failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
                'message' => 'Authentication failed: ' . $e->getMessage()
            ], 401);
        }
    }

    /**
     * Verify Microsoft Entra ID JWT Token
     *
     * @param string $jwt
     * @return object Decoded JWT payload
     * @throws Exception
     */
    protected function verifyToken(string $jwt): object
    {
        $configuredTenantId = config('azure.tenant_id');
        $configuredClientId = config('azure.client_id');

        // Fetch JWKS public keys from Microsoft
        $jwks = $this->getMicrosoftJwks($configuredTenantId);

        // Convert JWKS array into Firebase JWT Key objects
        $parsedKeys = JWK::parseKeySet($jwks);

        // Verify signature & decode claims (handles exp, nbf, iat)
        $decoded = JWT::decode($jwt, $parsedKeys);

        // Validate Tenant ID (tid)
        if (!empty($configuredTenantId)) {
            $tokenTenantId = $decoded->tid ?? null;
            if ($tokenTenantId !== $configuredTenantId) {
                throw new Exception("Token issuer tenant '{$tokenTenantId}' does not match allowed organization tenant.");
            }
        }

        // Validate Audience (aud)
        if (!empty($configuredClientId)) {
            $tokenAudience = $decoded->aud ?? null;
            $allowedAudiences = [
                $configuredClientId,
                'api://' . $configuredClientId
            ];

            if (!in_array($tokenAudience, $allowedAudiences, true)) {
                throw new Exception("Token audience '{$tokenAudience}' is not authorized for this API.");
            }
        }

        // Validate Issuer (iss)
        if (!isset($decoded->iss)) {
            throw new Exception("Token missing issuer claim (iss).");
        }

        $validIssuerPrefixes = [
            'https://login.microsoftonline.com/',
            'https://sts.windows.net/'
        ];

        $isValidIssuer = false;
        foreach ($validIssuerPrefixes as $prefix) {
            if (str_starts_with($decoded->iss, $prefix)) {
                $isValidIssuer = true;
                break;
            }
        }

        if (!$isValidIssuer) {
            throw new Exception("Invalid token issuer: '{$decoded->iss}'.");
        }

        return $decoded;
    }

    /**
     * Retrieve Microsoft's JWKS public keys with caching.
     *
     * @param string|null $tenantId
     * @return array
     * @throws Exception
     */
    protected function getMicrosoftJwks(?string $tenantId): array
    {
        $cacheKey = 'azure_ad_jwks_' . ($tenantId ?: 'common');
        $cacheTtl = config('azure.cache_ttl', 86400);

        return Cache::remember($cacheKey, $cacheTtl, function () use ($tenantId) {
            $url = !empty($tenantId)
                ? "https://login.microsoftonline.com/{$tenantId}/discovery/v2.0/keys"
                : "https://login.microsoftonline.com/common/discovery/v2.0/keys";

            $response = Http::timeout(10)->get($url);

            if ($response->failed()) {
                throw new Exception("Failed to fetch Microsoft JWKS keys from {$url}");
            }

            $data = $response->json();
            if (empty($data['keys'])) {
                throw new Exception("Microsoft JWKS response contained no keys.");
            }

            return $data;
        });
    }
}
