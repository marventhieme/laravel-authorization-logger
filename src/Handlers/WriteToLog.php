<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Handlers;

use Illuminate\Support\Facades\Log;
use MarvenThieme\LaravelAuthorizationLogger\Contracts\LogHandler;
use MarvenThieme\LaravelAuthorizationLogger\Objects\LogData;

class WriteToLog implements LogHandler
{
    public function handle(LogData $logData): void
    {
        $channel = config('authorization-logger.log_channel', 'stack');

        $message = sprintf(
            '[%s] Authorization denied for %s on %s::%s',
            $logData->timestamp ?? now()->toIso8601String(),
            $logData->userContext->type ?? 'unknown',
            $logData->policyContext->policyClass ?? 'unknown',
            $logData->policyContext->policyMethod ?? 'unknown'
        );

        Log::channel($channel)->warning($message, $logData->toArray());
    }
}
