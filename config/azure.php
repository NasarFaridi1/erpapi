<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Microsoft Entra ID (Azure AD) Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for authenticating API requests using Microsoft Identity Platform
    | JWT tokens.
    |
    */

    'tenant_id' => env('AZURE_TENANT_ID'),

    'client_id' => env('AZURE_CLIENT_ID'),

    'cache_ttl' => env('AZURE_JWKS_CACHE_TTL', 86400), // 24 hours

    'key_discovery_url' => env(
        'AZURE_KEY_DISCOVERY_URL',
        'https://login.microsoftonline.com/' . (env('AZURE_TENANT_ID') ?: 'common') . '/discovery/v2.0/keys'
    ),
];
