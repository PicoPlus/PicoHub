<?php

namespace App\Services\HubSpot;

use App\Support\Logger;

class HubSpotLogger
{
    public static function requestStarted(string $operation, string $method, string $endpoint, array $context = []): float
    {
        Logger::info('hubspot', 'HubSpot API request started', array_merge([
            'operation' => $operation,
            'http_method' => $method,
            'endpoint' => $endpoint,
        ], self::sanitizeContext($context)));

        return microtime(true);
    }

    public static function requestSucceeded(
        string $operation,
        string $method,
        string $endpoint,
        int $status,
        array $responseBody,
        float $startedAt,
        array $context = [],
    ): void {
        Logger::info('hubspot', 'HubSpot API request succeeded', array_merge([
            'operation' => $operation,
            'http_method' => $method,
            'endpoint' => $endpoint,
            'status' => $status,
            'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
            'response_summary' => self::summarizeResponse($responseBody),
        ], self::sanitizeContext($context)));
    }

    public static function requestFailed(
        string $operation,
        string $method,
        string $endpoint,
        int $status,
        ?array $responseBody,
        string $raw,
        float $startedAt,
        ?\Throwable $exception = null,
        array $context = [],
    ): void {
        Logger::error('hubspot', 'HubSpot API request failed', array_merge([
            'operation' => $operation,
            'http_method' => $method,
            'endpoint' => $endpoint,
            'status' => $status,
            'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
            'response_body' => $responseBody,
            'response_raw_preview' => mb_substr($raw, 0, 2000),
            'exception' => $exception ? [
                'class' => $exception::class,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ] : null,
        ], self::sanitizeContext($context)));
    }

    public static function contactOperation(string $action, array $context = []): void
    {
        Logger::info('hubspot', 'HubSpot contact operation', array_merge([
            'action' => $action,
        ], self::sanitizeContext($context)));
    }

    public static function propertyMapping(string $sourceField, string $hubspotField, mixed $value, array $context = []): void
    {
        Logger::debug('hubspot', 'HubSpot property mapped', array_merge([
            'source_field' => $sourceField,
            'hubspot_property' => $hubspotField,
            'value_preview' => self::maskValue($hubspotField, $value),
        ], self::sanitizeContext($context)));
    }

    private static function summarizeResponse(array $body): array
    {
        $summary = [];

        if (isset($body['results']) && is_array($body['results'])) {
            $summary['result_count'] = count($body['results']);
            $summary['result_ids'] = array_values(array_filter(array_map(
                static fn ($item) => $item['id'] ?? null,
                array_slice($body['results'], 0, 10)
            )));
        }

        if (isset($body['id'])) {
            $summary['id'] = $body['id'];
        }

        if (isset($body['properties']) && is_array($body['properties'])) {
            $summary['property_keys'] = array_keys($body['properties']);
        }

        if (isset($body['total'])) {
            $summary['total'] = $body['total'];
        }

        return $summary;
    }

    private static function sanitizeContext(array $context): array
    {
        $sanitized = $context;

        foreach (['properties', 'payload', 'request_body', 'updates'] as $key) {
            if (!isset($sanitized[$key]) || !is_array($sanitized[$key])) {
                continue;
            }

            $sanitized[$key] = self::sanitizeProperties($sanitized[$key]);
        }

        if (isset($sanitized['token'])) {
            $sanitized['token'] = '***';
        }

        return $sanitized;
    }

    private static function sanitizeProperties(array $properties): array
    {
        $masked = [];

        foreach ($properties as $name => $value) {
            $masked[$name] = self::maskValue((string) $name, $value);
        }

        return $masked;
    }

    private static function maskValue(string $field, mixed $value): mixed
    {
        $lower = strtolower($field);

        if (in_array($lower, ['phone', 'mobile', 'email'], true) && is_string($value) && $value !== '') {
            return mb_substr($value, 0, 3) . '***' . mb_substr($value, -2);
        }

        if (in_array($lower, ['ncode', 'national_code', 'natcode'], true) && is_string($value) && strlen($value) >= 4) {
            return mb_substr($value, 0, 3) . '****' . mb_substr($value, -3);
        }

        return $value;
    }
}
