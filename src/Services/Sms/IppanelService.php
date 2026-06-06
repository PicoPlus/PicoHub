<?php

namespace App\Services\Sms;

use App\Support\FormatHelper;
use App\Support\HttpClient;
use App\Support\Logger;

class IppanelService
{
    public function sendOtp(string $mobile, string $otpCode): void
    {
        $method = config('ippanel.otp_method', 'pattern');

        if ($method === 'votp') {
            $this->sendVotp($mobile, $otpCode);
        } else {
            $this->sendPattern(
                config('ippanel.pattern_otp'),
                $mobile,
                [config('ippanel.otp_param', 'code') => $otpCode]
            );
        }
    }

    public function sendWelcome(string $mobile, string $firstName, string $lastName, ?string $customerId = null): void
    {
        $pattern = config('ippanel.pattern_welcome');

        if ($pattern === '') {
            Logger::info('ippanel', 'Welcome pattern not configured', compact('mobile', 'firstName', 'lastName'));

            return;
        }

        $this->sendPattern($pattern, $mobile, array_filter([
            'firstname' => $firstName,
            'lastname' => $lastName,
            'cid' => $customerId ?? '',
        ]));
    }

    public function sendDealClosed(string $mobile, string $firstName, string $lastName, string $dealId): void
    {
        $pattern = config('ippanel.pattern_deal_closed');

        if ($pattern === '') {
            return;
        }

        $this->sendPattern($pattern, $mobile, [
            'firstname' => $firstName,
            'lastname' => $lastName,
            'deal_id' => $dealId,
        ]);
    }

    private function sendPattern(string $patternCode, string $mobile, array $params): void
    {
        if ($patternCode === '') {
            Logger::warning('ippanel', 'Pattern code not configured — OTP logged for development', [
                'mobile' => $mobile,
                'params' => $params,
            ]);

            return;
        }

        if (!$this->isConfigured()) {
            Logger::warning('ippanel', 'IPPanel not configured — pattern SMS skipped', compact('mobile', 'params'));

            return;
        }

        $this->postSend([
            'sending_type' => 'pattern',
            'from_number' => config('ippanel.from_number'),
            'code' => $patternCode,
            'recipients' => [FormatHelper::toE164($mobile)],
            'params' => $params,
        ]);
    }

    private function sendVotp(string $mobile, string $otpCode): void
    {
        if (!$this->isConfigured(requireFrom: false)) {
            Logger::warning('ippanel', 'IPPanel not configured — OTP logged for development', [
                'mobile' => $mobile,
                'otp' => $otpCode,
            ]);

            return;
        }

        $this->postSend([
            'sending_type' => 'votp',
            'message' => $otpCode,
            'params' => [
                'recipients' => [FormatHelper::toE164($mobile)],
            ],
        ]);
    }

    private function postSend(array $payload): void
    {
        $apiKey = config('ippanel.api_key');
        $baseUrl = rtrim(config('ippanel.base_url'), '/');

        $response = HttpClient::request('POST', $baseUrl . '/api/send', [
            'headers' => ['Authorization' => $apiKey],
            'json' => $payload,
            'verify' => false,
        ]);

        $json = $response['body'] ?? [];

        if ($response['status'] < 200 || $response['status'] >= 300 || !($json['meta']['status'] ?? false)) {
            $message = $json['meta']['message'] ?? $response['raw'];
            Logger::error('ippanel', 'IPPanel send failed', ['status' => $response['status'], 'message' => $message]);
            throw new \RuntimeException('ارسال پیامک با خطا مواجه شد: ' . $message);
        }
    }

    private function isConfigured(bool $requireFrom = true): bool
    {
        if (config('ippanel.api_key') === '') {
            return false;
        }

        if ($requireFrom && config('ippanel.from_number') === '') {
            return false;
        }

        return true;
    }
}
