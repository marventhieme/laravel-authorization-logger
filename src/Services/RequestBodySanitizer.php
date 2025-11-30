<?php

namespace MarvenThieme\LaravelAuthorizationLogger\Services;

class RequestBodySanitizer
{
    public function sanitize(array $data): array
    {
        return $this->sanitizeRecursive($data);
    }

    public function exceedsMaxSize(string $jsonData): bool
    {
        return strlen($jsonData) > config('authorization-logger.max_body_size');
    }

    public function getMaxSizeInKb(): int
    {
        return (int) (config('authorization-logger.max_body_size') / 1024);
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

        foreach (config('authorization-logger.sensitive_fields') as $sensitiveField) {
            if (str_contains($fieldLower, $sensitiveField)) {
                return true;
            }
        }

        return false;
    }
}
