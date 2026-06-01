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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'brevo' => [
        'api_key' => env('BREVO_API_KEY'),
    ],

    'korapay' => [
        'public_key' => env('KORAPAY_PUBLIC_KEY'),
        'secret_key' => env('KORAPAY_SECRET_KEY'),
        'base_url'   => env('KORAPAY_BASE_URL', 'https://api.korapay.com/merchant/api/v1'),
    ],

    'shipbubble' => [
        'api_key'  => env('SHIPBUBBLE_API_KEY'),
        'base_url' => env('SHIPBUBBLE_BASE_URL', 'https://api.shipbubble.com/v1'),
    ],

    'tiktok' => [
        'access_token' => env('TIKTOK_ACCESS_TOKEN'),
        'pixel_id'     => env('TIKTOK_PIXEL_ID', 'D7R5SH3C77U6QAB80D90'),
        'currency'     => env('TIKTOK_CURRENCY', 'NGN'),
    ],

    'monnify' => [
        'api_key'       => env('MONNIFY_API_KEY'),
        'secret_key'    => env('MONNIFY_SECRET_KEY'),
        'contract_code' => env('MONNIFY_CONTRACT_CODE'),
        'base_url'      => env('MONNIFY_BASE_URL', 'https://sandbox.monnify.com'),
        'source_account' => env('MONNIFY_SOURCE_ACCOUNT'),
    ],

    'termii' => [
        'api_key'   => env('TERMII_API_KEY'),
        'base_url'  => env('TERMII_BASE_URL', 'https://v3.api.termii.com'),
        'sender_id' => env('TERMII_SENDER_ID', 'YourBrand'),
        'channel'   => env('TERMII_CHANNEL', 'dnd'),
    ],

];
