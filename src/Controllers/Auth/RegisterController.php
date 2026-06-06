<?php

namespace App\Controllers\Auth;

use App\Http\Request;
use App\Services\Auth\OtpService;
use App\Services\HubSpot\ContactService;
use App\Services\HubSpot\HubSpotClient;
use App\Services\HubSpot\HubSpotLogger;
use App\Services\Sms\SmsService;
use App\Services\Sms\IppanelService;
use App\Services\Zohal\ZohalService;
use App\Support\FormatHelper;
use App\Support\NationalCodeValidator;

/**
 * IMPORTANT:
 * OTP bypass should NOT live in controller.
 * It should be moved to middleware or bootstrap.
 */
function otp_bypass_if_disabled(): void
{
    if (function_exists('env') && env('OTP_ENABLED', true) === false) {
        $_SESSION['login_state'] = 1;
        $_SESSION['user_role'] = 'User';

        header('Location: /user/panel');
        exit;
    }
}

class RegisterController
{
    public static function show(): string
    {
        $registration = $_SESSION['registration'] ?? [
            'step' => 1,
            'national_code' => $_SESSION['pending_national_code'] ?? '',
        ];

        return view('auth.register', compact('registration'));
    }

    public static function verifyIdentity(Request $request): never
    {
        otp_bypass_if_disabled();

        $data = $request->validate([
            'national_code' => 'required|string|size:10',
            'birth_date' => 'required|string',
        ]);

        $nationalCode = trim($data['national_code']);

        if ($error = NationalCodeValidator::validate($nationalCode)) {
            remember_old($request->all());
            set_errors(['national_code' => [$error]]);
            redirect(url('/auth/register'));
            exit;
        }

        try {
            $zohal = new ZohalService();
            $inquiry = $zohal->nationalIdentityInquiry($nationalCode, $data['birth_date']);

            if (!$inquiry['success']) {
                remember_old($request->all());
                set_errors([
                    $inquiry['error_field'] ?? 'birth_date' =>
                        [$inquiry['message'] ?? 'Identity inquiry failed.']
                ]);
                redirect(url('/auth/register'));
                exit;
            }

            if (!$inquiry['matched']) {
                remember_old($request->all());
                set_errors([
                    'birth_date' => ['National code and birth date do not match.']
                ]);
                redirect(url('/auth/register'));
                exit;
            }

            $identity = $inquiry['data'];

            if (empty($identity['first_name']) || empty($identity['last_name'])) {
                remember_old($request->all());
                set_errors([
                    'birth_date' => ['Identity data not found. Please verify inputs.']
                ]);
                redirect(url('/auth/register'));
                exit;
            }

            $_SESSION['registration'] = [
                'step' => 2,
                'national_code' => $nationalCode,
                'birth_date' => $data['birth_date'],
                'first_name' => $identity['first_name'] ?? '',
                'last_name' => $identity['last_name'] ?? '',
                'father_name' => $identity['father_name'] ?? '',
                'gender' => $identity['gender'] ?? '',
                'alive' => $identity['alive'] ?? true,
            ];

            redirect(url('/auth/register'));
            exit;

        } catch (\Throwable $e) {
            remember_old($request->all());
            set_errors([
                'birth_date' => ['Identity service error. Please try again.']
            ]);
            redirect(url('/auth/register'));
            exit;
        }
    }

    public static function sendOtp(Request $request): never
    {
        otp_bypass_if_disabled();

        $registration = $_SESSION['registration'] ?? [];

        $data = $request->validate([
            'phone' => 'required|string|min:10'
        ]);

        $phone = FormatHelper::normalizePhone($data['phone']);
        $nationalCode = $registration['national_code'] ?? '';

        try {
            $zohal = new ZohalService();
            $shahkar = $zohal->shahkarInquiry($nationalCode, $phone);

            if (!$shahkar['success']) {
                remember_old($request->all());
                set_errors([
                    $shahkar['error_field'] ?? 'phone' =>
                        [$shahkar['message'] ?? 'Shahkar verification failed.']
                ]);
                redirect(url('/auth/register'));
                exit;
            }

            if (!$shahkar['matched']) {
                remember_old($request->all());
                set_errors([
                    'phone' => ['Phone number does not match national code.']
                ]);
                redirect(url('/auth/register'));
                exit;
            }

            $otpService = new OtpService();
            $code = $otpService->generate();
            $otpService->store($phone, $code);

            (new SmsService(new IppanelService()))->sendOtp($phone, $code);

            $_SESSION['registration'] = array_merge($registration, [
                'step' => 3,
                'phone' => $phone,
                'shahkar_status' => 'verified',
            ]);

            set_flash('success', 'OTP sent successfully.');
            redirect(url('/auth/register'));
            exit;

        } catch (\Throwable $e) {
            remember_old($request->all());
            set_errors(['phone' => ['Failed to send OTP. Please try again.']]);
            redirect(url('/auth/register'));
            exit;
        }
    }

    public static function verifyOtp(Request $request): never
    {
        otp_bypass_if_disabled();

        $registration = $_SESSION['registration'] ?? [];
        $phone = $registration['phone'] ?? '';

        $data = $request->validate([
            'otp_code' => 'required|string|size:6'
        ]);

        $otpService = new OtpService();
        $result = $otpService->validate($phone, $data['otp_code']);

        if (!$result['valid']) {
            set_errors(['otp_code' => [$result['message']]]);
            redirect(url('/auth/register'));
            exit;
        }

        try {
            $contacts = new ContactService(new HubSpotClient());

            $properties = [
                'firstname' => $registration['first_name'] ?? '',
                'lastname' => $registration['last_name'] ?? '',
                'phone' => $phone,
                'ncode' => $registration['national_code'] ?? '',
                'date_of_birth' => $registration['birth_date'] ?? '',
                'father_name' => $registration['father_name'] ?? '',
                'gender' => $registration['gender'] ?? '',
                'shahkar_status' => $registration['shahkar_status'] ?? 'verified',
            ];

            HubSpotLogger::contactOperation('register_create', [
                'ncode' => $properties['ncode'],
                'date_of_birth' => $properties['date_of_birth'],
                'father_name' => $properties['father_name'],
            ]);

            $contact = $contacts->create($properties);

            (new SmsService(new IppanelService()))->sendWelcome(
                $phone,
                $registration['first_name'] ?? '',
                $registration['last_name'] ?? '',
                $contact['id'] ?? null
            );

            unset($_SESSION['registration'], $_SESSION['pending_national_code']);

            $_SESSION['login_state'] = 1;
            $_SESSION['user_role'] = 'User';
            $_SESSION['contact'] = $contact;

            set_flash('success', 'Registration completed successfully.');
            redirect(url('/user/panel'));
            exit;

        } catch (\Throwable $e) {
            HubSpotLogger::requestFailed(
                'register_create',
                'POST',
                '/crm/v3/objects/contacts',
                0,
                null,
                '',
                microtime(true),
                $e,
                ['ncode' => $registration['national_code'] ?? null],
            );

            set_errors(['otp_code' => ['Failed to create user account.']]);
            redirect(url('/auth/register'));
            exit;
        }
    }
}