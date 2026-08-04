<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
    'stripe' => [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
],


    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'transactworld' => [
        'member_id'    => env('TRANSACTWORLD_MEMBER_ID'),
        'partner_id'   => env('TRANSACTWORLD_PARTNER_ID'),
        'terminal_id'  => env('TRANSACTWORLD_TERMINAL_ID'),
        'secret_key'   => env('TRANSACTWORLD_SECRET_KEY'),
        'currency'     => env('TRANSACTWORLD_CURRENCY', 'INR'),
        'callback_url' => env('TRANSACTWORLD_CALLBACK_URL'),
        'transaction_url' => env('TRANSACTWORLD_TRANSACTION_URL'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
