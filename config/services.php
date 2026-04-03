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

//    'postmark' => [
//        'key' => env('POSTMARK_API_KEY'),
//    ],
//
//    'resend' => [
//        'key' => env('RESEND_API_KEY'),
//    ],
//
//    'ses' => [
//        'key' => env('AWS_ACCESS_KEY_ID'),
//        'secret' => env('AWS_SECRET_ACCESS_KEY'),
//        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
//    ],
//
//    'slack' => [
//        'notifications' => [
//            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
//            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
//        ],
//    ],

    // Dynamic Socialite provider configuration
    // Set SSO_PROVIDER in .env (e.g., 'google', 'github', 'authentik')
    // For OIDC providers (Authentik, Keycloak, etc), set SSO_BASE_URL for auto-discovery
    // For plain OAuth2 providers (GitHub, Facebook), set individual URLs
    env('SSO_PROVIDER') => [
            'enabled' => env('SSO_ENABLED', false),
            'client_id' => env('SSO_CLIENT_ID'),
            'client_secret' => env('SSO_CLIENT_SECRET'),
            'redirect' => env('APP_URL') . '/auth/callback',
            'base_url' => env('SSO_BASE_URL'), // OIDC auto-discovery (optional)
            'authorize_url' => env('SSO_AUTHORIZE_URL'), // Manual override (optional)
            'token_url' => env('SSO_TOKEN_URL'), // Manual override (optional)
            'userinfo_url' => env('SSO_USERINFO_URL'), // Manual override (optional)
            'name' => env('SSO_NAME'),

            'guzzle' => ['verify' => env('SSO_VERIFY_SSL', true) ],
        ],

];
