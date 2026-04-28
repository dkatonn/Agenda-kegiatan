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

    'tata_usaha_agenda' => [
        'base_url' => env('TATA_USAHA_API_BASE_URL'),
        'token' => env('TATA_USAHA_API_TOKEN'),
        'timeout' => env('TATA_USAHA_API_TIMEOUT', 5),
    ],

    'kemendagri_pegawai' => [
        'base_url' => env('KEMENDAGRI_API_BASE_URL', 'https://apimanager-ropeg.kemendagri.go.id'),
        'username' => env('KEMENDAGRI_API_USER'),
        'password' => env('KEMENDAGRI_API_PASS'),
        'timeout' => env('KEMENDAGRI_API_TIMEOUT', 10),
        'verify_ssl' => env('KEMENDAGRI_API_VERIFY_SSL', true),
    ],

];
