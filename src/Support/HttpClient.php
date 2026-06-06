<?php

namespace App\Support;

class HttpClient
{
    /**
     * @return array{status: int, body: array<string, mixed>|null, raw: string, duration_ms: float}
     */
    public static function request(
        string $method,
        string $url,
        array $options = [],
    ): array {
        $headers = $options['headers'] ?? [];
        $json = $options['json'] ?? null;
        $query = $options['query'] ?? [];
        $timeout = $options['timeout'] ?? 30;
        $verifySsl = $options['verify'] ?? true;

        if ($query !== []) {
            $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($query);
        }

        $ch = curl_init($url);
        $curlHeaders = [];

        foreach ($headers as $name => $value) {
            $curlHeaders[] = $name . ': ' . $value;
        }

        if ($json !== null) {
            $curlHeaders[] = 'Content-Type: application/json';
            $curlHeaders[] = 'Accept: application/json';
        }

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_HTTPHEADER => $curlHeaders,
            CURLOPT_SSL_VERIFYPEER => $verifySsl,
            CURLOPT_SSL_VERIFYHOST => $verifySsl ? 2 : 0,
        ]);

        if ($json !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json, JSON_UNESCAPED_UNICODE));
        }

        $start = microtime(true);
        $raw = curl_exec($ch);
        $durationMs = round((microtime(true) - $start) * 1000, 2);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new \RuntimeException('HTTP request failed: ' . $error);
        }

        $decoded = json_decode($raw, true);

        return [
            'status' => $status,
            'body' => is_array($decoded) ? $decoded : null,
            'raw' => $raw,
            'duration_ms' => $durationMs,
        ];
    }

    public static function throwIfFailed(array $response, string $message = 'HTTP request failed'): void
    {
        if ($response['status'] < 200 || $response['status'] >= 300) {
            $detail = $response['body']['message'] ?? $response['raw'] ?? '';
            throw new \RuntimeException($message . ' (HTTP ' . $response['status'] . '): ' . $detail);
        }
    }
}
