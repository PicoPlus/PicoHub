<?php

namespace App\Controllers\Admin;

use App\Support\HttpClient;
use App\Support\Logger;

class SmsController
{
    public static function index(): string
    {
        $config = [
            'api_key' => config('ippanel.api_key'),
            'base_url' => config('ippanel.base_url'),
            'from_number' => config('ippanel.from_number'),
            'otp_method' => config('ippanel.otp_method'),
            'otp_param' => config('ippanel.otp_param'),
            'pattern_otp' => config('ippanel.pattern_otp'),
            'pattern_welcome' => config('ippanel.pattern_welcome'),
            'pattern_deal_closed' => config('ippanel.pattern_deal_closed'),
        ];

        $credit = null;
        $error = null;

        if ($config['api_key'] !== '') {
            try {
                $response = HttpClient::request('GET', rtrim($config['base_url'], '/') . '/api/user', [
                    'headers' => ['Authorization' => $config['api_key']],
                    'verify' => false,
                ]);
                if ($response['status'] >= 200 && $response['status'] < 300) {
                    $credit = $response['body']['data']['credit'] ?? null;
                }
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }
        }

        return view('admin.pages.sms-config', compact('config', 'credit', 'error'));
    }

    public static function sendTest(): string
    {
        $phone = trim($_POST['phone'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($phone === '' || $message === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'شماره و متن پیام الزامی است.'];
            return redirect(url('/admin/sms'));
        }

        $apiKey = config('ippanel.api_key');
        if ($apiKey === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'کلید API تنظیم نشده است.'];
            return redirect(url('/admin/sms'));
        }

        try {
            $baseUrl = rtrim(config('ippanel.base_url'), '/');
            $fromNumber = config('ippanel.from_number');

            $response = HttpClient::request('POST', $baseUrl . '/api/send', [
                'headers' => [
                    'Authorization' => $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'sending_type' => 'webservice',
                    'from_number' => $fromNumber,
                    'message' => $message,
                    'recipients' => [$phone],
                ],
                'verify' => false,
            ]);

            $body = $response['body'] ?? [];
            if ($response['status'] >= 200 && $response['status'] < 300 && ($body['meta']['status'] ?? false)) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'پیامک با موفقیت ارسال شد.'];
                Logger::info('sms', 'Test SMS sent', ['phone' => $phone]);
            } else {
                $msg = $body['meta']['message'] ?? 'خطای ناشناخته';
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا در ارسال: ' . $msg];
            }
        } catch (\Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا: ' . $e->getMessage()];
        }

        return redirect(url('/admin/sms'));
    }

    public static function sendPattern(): string
    {
        $phone = trim($_POST['phone'] ?? '');
        $patternCode = trim($_POST['pattern_code'] ?? '');
        $paramsRaw = trim($_POST['params'] ?? '{}');

        if ($phone === '' || $patternCode === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'شماره و کد پترن الزامی است.'];
            return redirect(url('/admin/sms'));
        }

        $params = json_decode($paramsRaw, true);
        if (!is_array($params)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'پارامترها باید JSON معتبر باشد.'];
            return redirect(url('/admin/sms'));
        }

        $apiKey = config('ippanel.api_key');
        if ($apiKey === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'کلید API تنظیم نشده است.'];
            return redirect(url('/admin/sms'));
        }

        try {
            $baseUrl = rtrim(config('ippanel.base_url'), '/');
            $fromNumber = config('ippanel.from_number');

            $response = HttpClient::request('POST', $baseUrl . '/api/send', [
                'headers' => [
                    'Authorization' => $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'sending_type' => 'pattern',
                    'from_number' => $fromNumber,
                    'code' => $patternCode,
                    'recipients' => [$phone],
                    'params' => $params,
                ],
                'verify' => false,
            ]);

            $body = $response['body'] ?? [];
            if ($response['status'] >= 200 && $response['status'] < 300 && ($body['meta']['status'] ?? false)) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'پیامک پترن با موفقیت ارسال شد.'];
            } else {
                $msg = $body['meta']['message'] ?? 'خطای ناشناخته';
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا: ' . $msg];
            }
        } catch (\Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا: ' . $e->getMessage()];
        }

        return redirect(url('/admin/sms'));
    }
}
