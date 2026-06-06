<?php

namespace App\Services\Zohal;

use App\Support\HttpClient;
use App\Support\Logger;

class ZohalService
{
    public function nationalIdentityInquiry(string $nationalCode, string $birthDate): array
    {
        return $this->inquiry('national_identity_inquiry', [
            'national_code' => $nationalCode,
            'birth_date' => $birthDate,
        ]);
    }

    public function shahkarInquiry(string $nationalCode, string $mobile): array
    {
        return $this->inquiry('shahkar', [
            'national_code' => $nationalCode,
            'mobile' => $mobile,
        ]);
    }

    private function inquiry(string $endpoint, array $payload): array
    {
        $token = config('zohal.token');

        if ($token === '') {
            throw new \RuntimeException('ZOHAL_TOKEN is not configured.');
        }

        $url = rtrim(config('zohal.base_url'), '/') . '/v0/services/inquiry/' . $endpoint;

        try {
            $response = HttpClient::request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
                'verify' => false,
            ]);

            if ($response['status'] === 400) {
                return $this->mapHttp400($response['body'] ?? [], $payload);
            }

            HttpClient::throwIfFailed($response, 'Zohal inquiry failed');

            return $this->mapSuccessBody($response['body'] ?? []);
        } catch (\Throwable $e) {
            Logger::warning('zohal', 'Zohal inquiry failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'matched' => false,
                'data' => [],
                'message' => 'خطا در ارتباط با سرویس زحل.',
                'error_field' => null,
            ];
        }
    }

    private function mapSuccessBody(array $body): array
    {
        $result = (int) ($body['result'] ?? 0);
        $responseBody = $body['response_body'] ?? [];
        $data = $responseBody['data'] ?? [];
        $message = $responseBody['message'] ?? null;

        if ($result !== 1) {
            return [
                'success' => false,
                'matched' => false,
                'data' => [],
                'message' => $message ?: 'درخواست استعلام با خطا مواجه شد.',
                'error_field' => null,
            ];
        }

        return [
            'success' => true,
            'matched' => (bool) ($data['matched'] ?? false),
            'data' => $this->normalizeIdentityData($data),
            'message' => $message,
            'error_field' => null,
        ];
    }

    private function mapHttp400(array $body, array $payload): array
    {
        $message = $body['response_body']['message']
            ?? $body['message']
            ?? 'درخواست نامعتبر است.';

        $errorField = null;
        $lower = mb_strtolower($message);

        if (str_contains($lower, 'national') || str_contains($message, 'کد ملی')) {
            $errorField = 'national_code';
            $message = 'کد ملی نامعتبر است.';
        } elseif (str_contains($lower, 'birth') || str_contains($message, 'تاریخ تولد')) {
            $errorField = 'birth_date';
            $message = 'تاریخ تولد نامعتبر است.';
        } elseif (isset($payload['mobile'])) {
            $errorField = 'phone';
        }

        return [
            'success' => false,
            'matched' => false,
            'data' => [],
            'message' => $message,
            'error_field' => $errorField,
        ];
    }

    private function normalizeIdentityData(array $data): array
    {
        return [
            'matched' => $data['matched'] ?? null,
            'first_name' => $data['first_name'] ?? $data['firstName'] ?? null,
            'last_name' => $data['last_name'] ?? $data['lastName'] ?? null,
            'father_name' => $data['father_name'] ?? $data['fatherName'] ?? null,
            'national_code' => $data['national_code'] ?? $data['nationalCode'] ?? null,
            'alive' => $data['alive'] ?? (isset($data['is_dead']) ? !$data['is_dead'] : null),
            'is_dead' => $data['is_dead'] ?? null,
            'gender' => $data['gender'] ?? null,
        ];
    }
}
