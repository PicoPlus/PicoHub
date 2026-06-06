<?php

namespace App\Support;

class NationalCodeValidator
{
    public static function validate(string $nationalCode): ?string
    {
        $nationalCode = trim($nationalCode);

        if ($nationalCode === '') {
            return 'کد ملی نمی‌تواند خالی باشد';
        }

        if (strlen($nationalCode) !== 10) {
            return 'کد ملی باید ده رقم باشد';
        }

        if (!ctype_digit($nationalCode)) {
            return 'کد ملی می‌تواند فقط شامل اعداد باشد';
        }

        if (!self::isValidChecksum($nationalCode)) {
            return 'کد ملی معتبر نیست';
        }

        return null;
    }

    public static function isValidChecksum(string $nationalCode): bool
    {
        if (strlen($nationalCode) !== 10) {
            return false;
        }

        if (preg_match('/^(\d)\1{9}$/', $nationalCode)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $nationalCode[$i] * (10 - $i);
        }

        $remainder = $sum % 11;
        $checkDigit = $remainder < 2 ? $remainder : 11 - $remainder;

        return $checkDigit === (int) $nationalCode[9];
    }
}
