<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS Provider
    |--------------------------------------------------------------------------
    |
    | The driver used to deliver one-time codes for two-factor authentication.
    | Supported values: "meta" (WhatsApp Cloud API) and "log" (development
    | only — writes the code to the Laravel log).
    |
    */

    'provider' => env('SMS_PROVIDER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Verification Code Settings
    |--------------------------------------------------------------------------
    |
    | These options control the one-time codes used during two-factor
    | authentication: their length, how long they remain valid, how many
    | attempts are allowed before the code is revoked, and the minimum
    | number of seconds between (re)sends for a single user.
    |
    */

    'code' => [
        'length' => (int) env('SMS_CODE_LENGTH', 6),
        'expires_minutes' => (int) env('SMS_CODE_EXPIRES_MINUTES', 10),
        'max_attempts' => (int) env('SMS_CODE_MAX_ATTEMPTS', 5),
        'throttle_seconds' => (int) env('SMS_CODE_THROTTLE_SECONDS', 30),
    ],

];
