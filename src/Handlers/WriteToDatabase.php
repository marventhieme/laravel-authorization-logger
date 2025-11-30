<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Handlers;

use MarvenThieme\LaravelAuthorizationLogger\Contracts\LogHandler;
use MarvenThieme\LaravelAuthorizationLogger\Models\AuthorizationDenial;
use MarvenThieme\LaravelAuthorizationLogger\Objects\LogData;

class WriteToDatabase implements LogHandler
{
    public function handle(LogData $logData): void
    {
        try {
            AuthorizationDenial::create([
                'event' => $logData->event,
                'logged_at' => $logData->timestamp,

                // User context
                'user_id' => $logData->userContext->userId,
                'user_ip_address' => $logData->userContext->ipAddress,
                'user_roles' => $logData->userContext->roles,

                // Policy context
                'ability' => $logData->policyContext->ability,
                'policy_class' => $logData->policyContext->policyClass,
                'policy_method' => $logData->policyContext->policyMethod,
                'model_class' => $logData->policyContext->modelClass,
                'model_id' => $logData->policyContext->modelId,

                // Request context
                'request_method' => $logData->requestContext->method,
                'request_url' => $logData->requestContext->url,
                'request_endpoint' => $logData->requestContext->endpoint,
                'request_route_name' => $logData->requestContext->routeName,
                'request_body' => $logData->requestContext->body,
            ]);
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                throw $e;
            }
        }
    }
}
