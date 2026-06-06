<?php

namespace App\Services\Auth;

use App\Support\FileCache;
use App\Support\FormatHelper;

class OtpService
{
    public function generate(): string
    {
        return (string) random_int(100000, 999999);
    }

    public function store(string $phone, string $code): void
    {
        $ttl = config('otp.ttl_minutes', 5) * 60;

        FileCache::put($this->cacheKey($phone), [
            'code' => $code,
            'attempts' => 0,
        ], $ttl);
    }

    public function validate(string $phone, string $enteredCode): array
    {
        $data = FileCache::get($this->cacheKey($phone));

        if (!$data) {
            return ['valid' => false, 'message' => 'کد تأیید یافت نشد. لطفاً دوباره درخواست دهید.'];
        }

        $data['attempts']++;
        $maxAttempts = config('otp.max_attempts', 3);
        $ttl = config('otp.ttl_minutes', 5) * 60;

        if ($data['attempts'] > $maxAttempts) {
            FileCache::forget($this->cacheKey($phone));

            return ['valid' => false, 'message' => 'تعداد تلاش‌ها بیش از حد مجاز است. لطفاً دوباره درخواست دهید.'];
        }

        FileCache::put($this->cacheKey($phone), $data, $ttl);

        if (trim((string) $data['code']) !== trim($enteredCode)) {
            $remaining = $maxAttempts - $data['attempts'];

            return ['valid' => false, 'message' => "کد تأیید نامعتبر است. ({$remaining} تلاش باقی‌مانده)"];
        }

        FileCache::forget($this->cacheKey($phone));

        return ['valid' => true, 'message' => ''];
    }

    private function cacheKey(string $phone): string
    {
        return 'otp:' . FormatHelper::normalizePhone($phone);
    }
}
