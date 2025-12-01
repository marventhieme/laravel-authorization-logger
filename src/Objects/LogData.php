<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Objects;

class LogData
{
    public function __construct(
        public string $event,
        public string $timestamp,
        public UserContextData $userContext,
        public PolicyContextData $policyContext,
        public RequestContextData $requestContext,
    ) {}

    public function toArray(): array
    {
        return [
            'event' => $this->event,
            'timestamp' => $this->timestamp,
            'userContext' => [
                'type' => $this->userContext->type,
                'ipAddress' => $this->userContext->ipAddress,
                'id' => $this->userContext->userId,
                'roles' => $this->userContext->roles,
            ],
            'policyContext' => [
                'ability' => $this->policyContext->ability,
                'policyClass' => $this->policyContext->policyClass,
                'policyMethod' => $this->policyContext->policyMethod,
                'modelClass' => $this->policyContext->modelClass,
                'modelId' => $this->policyContext->modelId,
            ],
            'requestContext' => [
                'method' => $this->requestContext->method,
                'url' => $this->requestContext->url,
                'endpoint' => $this->requestContext->endpoint,
                'body' => $this->requestContext->body,
                'routeName' => $this->requestContext->routeName,
                'referrer' => $this->requestContext->referrer,
            ],
        ];
    }

    public function __toString(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
}
