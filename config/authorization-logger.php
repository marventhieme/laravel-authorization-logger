<?php

use MarvenThieme\LaravelAuthorizationLogger\Handlers\DebugToRay;

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
    ],

    /**
     * HTTP methods to skip logging for
     *
     * Common use case: ['GET', 'HEAD'] to skip UI permission checks
     */
    'skip_methods' => [],

    /**
     * This will check, whether the stack trace contains a Resource class.
     * If set to true, authorization checks originating from Resource classes will be skipped.
     * An example are UI permission checks like `Gate::allows('viewAny', Model::class)` calls from a Resource.
     */
    'skip_resource_checks' => true,

    /**
     * Sensitive fields to filter from request bodies
     *
     * These fields will be replaced with '[FILTERED]' in the log output
     */
    'sensitive_fields' => [
        'password',
        'token',
        'secret',
    ],

    /**
     * Maximum request body size in bytes
     */
    'max_body_size' => env('AUTHORIZATION_LOGGING_MAX_BODY_SIZE', 10240),

    /**
     * WriteToLog handler configuration
     */
    'log_channel' => env('AUTHORIZATION_LOGGING_CHANNEL', 'stack'),

    /**
     * DebugToRay handler configuration
     */
    'ray_only_local' => true,

    /**
     * ReportToFlare handler configuration
     */
    'flare_only_production' => true,
];
