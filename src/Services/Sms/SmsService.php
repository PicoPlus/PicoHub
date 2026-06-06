<?php

namespace App\Services\Sms;

class SmsService
{
    public function __construct(private readonly IppanelService $ippanel) {}

    public function sendOtp(string $mobile, string $otpCode): void
    {
        $this->ippanel->sendOtp($mobile, $otpCode);
    }

    public function sendWelcome(string $mobile, string $firstName, string $lastName, ?string $customerId = null): void
    {
        $this->ippanel->sendWelcome($mobile, $firstName, $lastName, $customerId);
    }

    public function sendDealClosed(string $mobile, string $firstName, string $lastName, string $dealId): void
    {
        $this->ippanel->sendDealClosed($mobile, $firstName, $lastName, $dealId);
    }
}
