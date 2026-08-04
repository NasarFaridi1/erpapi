<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Static OAuth 2.0 Credentials & JWT Settings
    |--------------------------------------------------------------------------
    */

    'jwt_secret' => env('OAUTH_JWT_SECRET', env('APP_KEY', 'super_secret_jwt_key_for_erp_api_2026')),

    'token_ttl' => env('OAUTH_TOKEN_TTL', 3600), // Token lifetime (1 hour)

    /*
    | Direct Static Allowed Clients
    | You can add as many static client credential pairs here as needed.
    */
    'clients' => [
        [
            'client_id'     => env('OAUTH_CLIENT_ID', 'powerbi_client_2026'),
            'client_secret' => env('OAUTH_CLIENT_SECRET', 'sec_erp_api_9823472398472938'),
            'name'          => 'PowerBI Organization Client',
        ],
    ],
];
