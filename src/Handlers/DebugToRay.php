<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Handlers;

use MarvenThieme\LaravelAuthorizationLogger\Contracts\LogHandler;
use MarvenThieme\LaravelAuthorizationLogger\Objects\LogData;

class DebugToRay implements LogHandler
{
    public function handle(LogData $logData): void
    {
        if (! function_exists('ray')) {
            return;
        }

        ray($logData)->color('orange');
    }
}
