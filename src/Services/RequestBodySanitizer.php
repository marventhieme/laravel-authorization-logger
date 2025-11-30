<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Services;

class RequestBodySanitizer
{
    protected const SENSITIVE_FIELDS = [
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
    ];

    protected const MAX_BODY_SIZE = 10240; // 10 KB

    public function sanitize(array $data): array
    {
        return $this->sanitizeRecursive($data);
    }

    public function exceedsMaxSize(string $jsonData): bool
    {
        return strlen($jsonData) > self::MAX_BODY_SIZE;
    }

    public function getMaxSizeInKb(): int
    {
        return (int) (self::MAX_BODY_SIZE / 1024);
    }

    protected function sanitizeRecursive(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if ($this->isSensitiveField($key)) {
                $sanitized[$key] = '[FILTERED]';
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeRecursive($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    protected function isSensitiveField(string $fieldName): bool
    {
        $fieldLower = strtolower($fieldName);

        foreach (self::SENSITIVE_FIELDS as $sensitiveField) {
            if (str_contains($fieldLower, $sensitiveField)) {
                return true;
            }
        }

        return false;
    }
}
