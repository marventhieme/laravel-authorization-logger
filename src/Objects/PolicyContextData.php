<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Objects;

class PolicyContextData
{
    public function __construct(
        public string $ability,
        public ?string $policyClass = null,
        public ?string $policyMethod = null,
        public ?string $modelClass = null,
        public ?string $modelId = null,
    ) {}
}
