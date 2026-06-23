<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ExchangeRate-API (https://www.exchangerate-api.com)
    |--------------------------------------------------------------------------
    |
    | Syncs extra_currency rates from the API into admin settings.
    | Run manually: php artisan currency:sync-rates
    | Scheduled daily when EXCHANGERATE_SYNC_ENABLED=true.
    |
    */

    'exchangerate_api' => [
        'enabled' => env('EXCHANGERATE_SYNC_ENABLED', true),
        'key' => env('EXCHANGERATE_API_KEY'),
        'base_url' => env('EXCHANGERATE_API_URL', 'https://v6.exchangerate-api.com/v6'),
        'open_url' => env('EXCHANGERATE_OPEN_URL', 'https://open.er-api.com/v6'),
        'use_open_when_no_key' => env('EXCHANGERATE_USE_OPEN_API', false),
        'fallback_base' => env('EXCHANGERATE_FALLBACK_BASE', 'USD'),
        'timeout' => (int) env('EXCHANGERATE_TIMEOUT', 15),

        /*
         * Optional: force INR per 1 USD (e.g. 94.992646850672 for 1413 USD = 134224.61 INR).
         * Overrides API for sync. Main USD → stored rate = 1/value; main INR + extra USD → rate = value.
         */
        'inr_per_usd' => env('EXCHANGERATE_INR_PER_USD') ? (float) env('EXCHANGERATE_INR_PER_USD') : null,
    ],

];
