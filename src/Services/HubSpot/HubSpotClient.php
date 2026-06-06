<?php

namespace App\Services\HubSpot;

use App\Support\HttpClient;

class HubSpotClient
{
    /**
     * @return array{status: int, body: array<string, mixed>|null, raw: string, duration_ms: float}
     */
    public function request(
        string $operation,
        string $method,
        string $endpoint,
        array $options = [],
        array $logContext = [],
    ): array {
        $token = config('hubspot.token');

        if ($token === '') {
            throw new \RuntimeException('HUBSPOT_TOKEN is not configured.');
        }

        $url = rtrim(config('hubspot.base_url'), '/') . $endpoint;
        $options['headers'] = array_merge($options['headers'] ?? [], [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]);
        $options['verify'] = false;

        $startedAt = HubSpotLogger::requestStarted($operation, $method, $endpoint, array_merge($logContext, [
            'request' => [
                'query' => $options['query'] ?? null,
                'json' => $options['json'] ?? null,
            ],
        ]));

        try {
            $response = HttpClient::request($method, $url, $options);

            if ($response['status'] >= 200 && $response['status'] < 300) {
                HubSpotLogger::requestSucceeded(
                    $operation,
                    $method,
                    $endpoint,
                    $response['status'],
                    $response['body'] ?? [],
                    $startedAt,
                    $logContext,
                );
            } else {
                HubSpotLogger::requestFailed(
                    $operation,
                    $method,
                    $endpoint,
                    $response['status'],
                    $response['body'],
                    $response['raw'],
                    $startedAt,
                    null,
                    $logContext,
                );
            }

            return $response;
        } catch (\Throwable $e) {
            HubSpotLogger::requestFailed(
                $operation,
                $method,
                $endpoint,
                0,
                null,
                '',
                $startedAt,
                $e,
                $logContext,
            );

            throw $e;
        }
    }

    public function throwIfFailed(array $response, string $message = 'HubSpot API request failed'): void
    {
        HttpClient::throwIfFailed($response, $message);
    }
}
