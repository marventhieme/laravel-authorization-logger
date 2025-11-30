<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Objects;

class UserContextData
{
    public function __construct(
        public string $type,
        public string $ipAddress,
        public ?string $userId = null,
        public ?array $roles = null,
    ) {}
}
