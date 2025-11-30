<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Objects;

class UserContextData
{
    public function __construct(
        public string $type,
        public string $ipAddress,
        public ?string $id = null,
        public ?string $email = null,
        public ?string $name = null,
        public ?array $roles = null,
    ) {}
}
