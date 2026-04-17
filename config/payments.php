<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Checkout driver
    |--------------------------------------------------------------------------
    |
    | demo: instant "payment" for local development and automated tests.
    | stripe: redirect to Stripe Checkout, return URL completes enrollment.
    | paypal: redirect to PayPal approval, return URL captures and enrolls.
    |
    */

    /**
     * Legacy default when checkout does not send payment_method (tests / old clients).
     */
    'gateway' => env('PAYMENT_GATEWAY', 'demo'),

    /*
     * Allow "demo" instant payment on the checkout form.
     * Defaults to ON in local so checkout works without Stripe/PayPal keys.
     * Set PAYMENT_DEMO_ENABLED=false in .env to hide it locally.
     */
    'demo_enabled' => env('PAYMENT_DEMO_ENABLED') !== null
        ? filter_var(env('PAYMENT_DEMO_ENABLED'), FILTER_VALIDATE_BOOLEAN)
        : env('APP_ENV') === 'local',

    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
        'publishable_key' => env('STRIPE_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    /*
     * PayPal REST credentials live in config/paypal.php (srmklive/paypal).
     * Use PAYPAL_CLIENT_ID / PAYPAL_SECRET or PAYPAL_SANDBOX_* / PAYPAL_LIVE_*.
     */

];
