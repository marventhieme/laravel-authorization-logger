<?php

use MarvenThieme\LaravelAuthorizationLogger\Handlers\DebugToRay;
use MarvenThieme\LaravelAuthorizationLogger\Handlers\WriteToDatabase;

// use MarvenThieme\LaravelAuthorizationLogger\Handlers\WriteToLog;

return [
    /**
     * Globally enable or disable authorization logging
     */
    'enabled' => env('AUTHORIZATION_LOGGING_ENABLED', true),

    /**
     * Log handlers pipeline
     *
     * The log data will be passed through each handler in order.
     * You can add your own custom handlers or remove default ones.
     * All handlers must implement the LogHandler contract.
     *
     * You may define your own handler. It must implement the LogHandler contract.
     */
    'handlers' => [
        DebugToRay::class,
        // WriteToLog::class,
        WriteToDatabase::class,
    ],

    /**
     * HTTP methods to skip logging for
     *
     * Example: ['GET', 'HEAD'] to skip UI permission checks
     */
    'http_methods_to_ignore' => [],

    /**
     * This will check, whether the stack trace contains a Resource class.
     * If set to true, authorization checks originating from Resource classes will be skipped.
     *
     * An example are UI permission checks like `Gate::allows('viewAny', Model::class)` calls from a Resource.
     */
    'classes_to_ignore' => [
        \Illuminate\Http\Resources\Json\JsonResource::class,
    ],

    /**
     * Sensitive fields to filter from request bodies
     *
     * These fields will be replaced with '[FILTERED]' in the log output
     */
    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'api_token',
        'access_token',
        'refresh_token',
        'secret',
        'api_secret',
        'private_key',
        'authorization',
        'bearer',
        'card_number',
        'cvv',
        'ssn',
        'credit_card',
    ],

    /**
     * Maximum request body size in bytes
     */
    'max_body_size' => env('AUTHORIZATION_LOGGING_MAX_BODY_SIZE', 10240),

    /**
     * WriteToLog handler configuration
     */
    'log_channel' => env('AUTHORIZATION_LOGGING_CHANNEL', 'daily'),

    'database' => [
        /**
         * Retention period for authorization denial logs in days
         */
        'prunable_after_days' => env('AUTHORIZATION_LOGGING_PRUNABLE_AFTER_DAYS', 30),
    ],
];
