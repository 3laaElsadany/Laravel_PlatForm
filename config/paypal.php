<?php

/**
 * PayPal REST credentials for srmklive/paypal (same pattern as PayPalController examples).
 *
 * Supports existing .env keys PAYPAL_CLIENT_ID / PAYPAL_SECRET, or per-environment
 * PAYPAL_SANDBOX_* / PAYPAL_LIVE_* used by the package docs.
 */
return [
    'mode' => env('PAYPAL_MODE', 'sandbox'),

    'sandbox' => [
        'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID', env('PAYPAL_CLIENT_ID')),
        'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET', env('PAYPAL_SECRET')),
        'app_id' => env('PAYPAL_SANDBOX_APP_ID', 'APP-80W284485P519543T'),
    ],

    'live' => [
        'client_id' => env('PAYPAL_LIVE_CLIENT_ID', env('PAYPAL_CLIENT_ID')),
        'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET', env('PAYPAL_SECRET')),
        'app_id' => env('PAYPAL_LIVE_APP_ID', ''),
    ],

    'payment_action' => env('PAYPAL_PAYMENT_ACTION', 'Sale'),
    'currency' => env('PAYPAL_CURRENCY', 'USD'),
    'notify_url' => env('PAYPAL_NOTIFY_URL', ''),
    'locale' => env('PAYPAL_LOCALE', 'en_US'),
    'validate_ssl' => filter_var(env('PAYPAL_VALIDATE_SSL', true), FILTER_VALIDATE_BOOLEAN),
];
