<?php

namespace App\Controllers\Admin;

use App\Http\Request;
use App\Services\Auth\OtpService;
use App\Services\HubSpot\ContactService;
use App\Services\HubSpot\HubSpotClient;
use App\Services\HubSpot\HubSpotLogger;
use App\Services\Sms\IppanelService;
use App\Services\Zohal\ZohalService;
use App\Support\FormatHelper;
use App\Support\NationalCodeValidator;

class CreateContactController
{
    public static function show(): string
    {
        $registration = $_SESSION['admin_create_contact'] ?? ['step' => 1, 'national_code' => ''];

        return view('admin.pages.create-contact', compact('registration'));
    }

    public static function verifyIdentity(Request $request): never
    {
        $data = $request->validate([
            'national_code' => 'required|string|size:10',
            'birth_date' => 'required|string',
        ]);

        $nationalCode = trim($data['national_code']);

        if ($error = NationalCodeValidator::validate($nationalCode)) {
            remember_old($request->all());
            set_errors(['national_code' => [$error]]);
            redirect(url('/admin/contacts/create'));
        }

        // Check if contact already exists in HubSpot
        try {
            $contacts = new ContactService(new HubSpotClient());
            $search = $contacts->searchByNationalCode($nationalCode);
            $results = $search['results'] ?? [];

            if ($results !== []) {
                remember_old($request->all());
                set_flash('error', 'مخاطبی با این کد ملی قبلاً در HubSpot ثبت شده است.');
                redirect(url('/admin/contacts/create'));
            }
        } catch (\Throwable) {
            // Continue anyway if HubSpot search fails
        }

        try {
            $zohal = new ZohalService();
            $inquiry = $zohal->nationalIdentityInquiry($nationalCode, $data['birth_date']);

            if (!$inquiry['success']) {
                remember_old($request->all());
                set_errors([
                    $inquiry['error_field'] ?? 'birth_date' => [$inquiry['message'] ?? 'استعلام هویت ناموفق بود.']
                ]);
                redirect(url('/admin/contacts/create'));
            }

            if (!$inquiry['matched']) {
                remember_old($request->all());
                set_errors(['birth_date' => ['کد ملی و تاریخ تولد مطابقت ندارند.']]);
                redirect(url('/admin/contacts/create'));
            }

            $identity = $inquiry['data'];

            if (empty($identity['first_name']) || empty($identity['last_name'])) {
                remember_old($request->all());
                set_errors(['birth_date' => ['اطلاعات هویتی یافت نشد. ورودی‌ها را بررسی کنید.']]);
                redirect(url('/admin/contacts/create'));
            }

            $_SESSION['admin_create_contact'] = [
                'step' => 2,
                'national_code' => $nationalCode,
                'birth_date' => $data['birth_date'],
                'first_name' => $identity['first_name'] ?? '',
                'last_name' => $identity['last_name'] ?? '',
                'father_name' => $identity['father_name'] ?? '',
                'gender' => $identity['gender'] ?? '',
                'alive' => $identity['alive'] ?? true,
            ];

            redirect(url('/admin/contacts/create'));
        } catch (\Throwable $e) {
            remember_old($request->all());
            set_errors(['birth_date' => ['خطای سرویس هویت. لطفاً دوباره تلاش کنید.']]);
            redirect(url('/admin/contacts/create'));
        }
    }

    public static function sendOtp(Request $request): never
    {
        $registration = $_SESSION['admin_create_contact'] ?? [];

        $data = $request->validate([
            'phone' => 'required|string|min:10',
        ]);

        $phone = FormatHelper::normalizePhone($data['phone']);
        $nationalCode = $registration['national_code'] ?? '';

        try {
            $zohal = new ZohalService();
            $shahkar = $zohal->shahkarInquiry($nationalCode, $phone);

            if (!$shahkar['success']) {
                remember_old($request->all());
                set_errors([
                    $shahkar['error_field'] ?? 'phone' => [$shahkar['message'] ?? 'تأیید شاهکار ناموفق بود.']
                ]);
                redirect(url('/admin/contacts/create'));
            }

            if (!$shahkar['matched']) {
                remember_old($request->all());
                set_errors(['phone' => ['شماره موبایل با کد ملی مطابقت ندارد.']]);
                redirect(url('/admin/contacts/create'));
            }

            $otpService = new OtpService();
            $code = $otpService->generate();
            $otpService->store($phone, $code);

            $sms = new IppanelService();
            $sms->sendOtp($phone, $code);

            $_SESSION['admin_create_contact']['step'] = 3;
            $_SESSION['admin_create_contact']['phone'] = $phone;

            redirect(url('/admin/contacts/create'));
        } catch (\Throwable $e) {
            remember_old($request->all());
            set_errors(['phone' => ['خطا در ارسال کد تأیید. لطفاً دوباره تلاش کنید.']]);
            redirect(url('/admin/contacts/create'));
        }
    }

    public static function verifyOtp(Request $request): never
    {
        $registration = $_SESSION['admin_create_contact'] ?? [];
        $phone = $registration['phone'] ?? '';

        $data = $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $otpService = new OtpService();
        $result = $otpService->validate($phone, $data['otp_code']);

        if (!$result['valid']) {
            set_errors(['otp_code' => [$result['message']]]);
            redirect(url('/admin/contacts/create'));
        }

        $_SESSION['admin_create_contact']['step'] = 4;

        redirect(url('/admin/contacts/create'));
    }

    public static function store(Request $request): never
    {
        $registration = $_SESSION['admin_create_contact'] ?? [];

        $email = trim($request->input('email') ?? '');
        $contactPlan = trim($request->input('contact_plan') ?? '');

        $properties = [
            'firstname' => $registration['first_name'] ?? '',
            'lastname' => $registration['last_name'] ?? '',
            'ncode' => $registration['national_code'] ?? '',
            'date_of_birth' => $registration['birth_date'] ?? '',
            'father_name' => $registration['father_name'] ?? '',
            'phone' => $registration['phone'] ?? '',
        ];

        if (!empty($registration['gender'])) {
            $properties['gender'] = (string) $registration['gender'];
        }

        if ($email !== '') {
            $properties['email'] = $email;
        }

        if ($contactPlan !== '') {
            $properties['contact_plan'] = $contactPlan;
        }

        try {
            $contacts = new ContactService(new HubSpotClient());

            HubSpotLogger::contactOperation('admin_create_contact', [
                'ncode' => $properties['ncode'],
                'admin' => session('admin_email', 'unknown'),
            ]);

            $contact = $contacts->create($properties);

            // Send welcome SMS
            try {
                $sms = new IppanelService();
                $sms->sendWelcome(
                    $properties['phone'],
                    $properties['firstname'],
                    $properties['lastname'],
                    $contact['id'] ?? null,
                );
            } catch (\Throwable) {
                // Non-critical
            }

            unset($_SESSION['admin_create_contact']);

            set_flash('success', 'مخاطب با موفقیت در HubSpot ایجاد شد. (ID: ' . ($contact['id'] ?? '—') . ')');
            redirect(url('/admin/contacts/create'));
        } catch (\Throwable $e) {
            set_flash('error', 'خطا در ایجاد مخاطب: ' . $e->getMessage());
            redirect(url('/admin/contacts/create'));
        }
    }

    public static function reset(): never
    {
        unset($_SESSION['admin_create_contact']);
        redirect(url('/admin/contacts/create'));
    }
}
