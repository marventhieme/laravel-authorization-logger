<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Objects;

class RequestContextData
{
    public function __construct(
        public string $method,
        public string $url,
        public string $endpoint,
        public ?string $body = null,
        public ?string $routeName = null,
        public ?string $referrer = null,
    ) {}
}
