<?php

namespace App\Support;

class FormatHelper
{
    private const PERSIAN_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    public static function formatNumber(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '۰';
        }

        $number = is_numeric($value) ? (float) $value : 0;
        $formatted = number_format($number, 0, '.', ',');

        return self::toPersianDigits($formatted);
    }

    public static function toPersianDigits(string $value): string
    {
        return str_replace(range('0', '9'), self::PERSIAN_DIGITS, $value);
    }

    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($digits, '98') && strlen($digits) === 12) {
            $digits = substr($digits, 2);
        }

        if (!str_starts_with($digits, '0') && strlen($digits) === 10) {
            $digits = '0' . $digits;
        }

        return $digits;
    }

    public static function toE164(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            $digits = '98' . substr($digits, 1);
        }

        if (!str_starts_with($digits, '98')) {
            $digits = '98' . $digits;
        }

        return '+' . $digits;
    }
}
