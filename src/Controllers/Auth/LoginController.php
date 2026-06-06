<?php

namespace App\Controllers\Auth;

use App\Http\Request;
use App\Services\Crm\ContactUpdateService;
use App\Services\HubSpot\ContactService;
use App\Services\HubSpot\HubSpotLogger;
use App\Support\NationalCodeValidator;

class LoginController
{
    public static function show(): string
    {
        clear_old();

        return view('auth.login', [
            'selectedRole' => old('role', 'User'),
        ]);
    }

    public static function login(Request $request): never
    {
        $data = $request->validate([
            'national_code' => 'required|string|size:10',
            'role' => 'required|in:User,Admin',
        ]);

        $nationalCode = trim($data['national_code']);

        if ($error = NationalCodeValidator::validate($nationalCode)) {
            remember_old($request->all());
            set_errors(['national_code' => [$error]]);
            redirect(url('/auth/login'));
        }

        if ($data['role'] === 'Admin') {
            redirect(url('/admin/login'));
        }

        try {
            $contacts = new ContactService(new \App\Services\HubSpot\HubSpotClient());
            $updater = new ContactUpdateService($contacts, new \App\Services\Zohal\ZohalService());

            HubSpotLogger::contactOperation('login_search', ['ncode' => $nationalCode]);

            $search = $contacts->searchByNationalCode($nationalCode);
            $results = $search['results'] ?? [];

            if ($results === []) {
                $_SESSION['pending_national_code'] = $nationalCode;
                set_flash('info', 'حسابی با این کد ملی یافت نشد. لطفاً ثبت‌نام کنید.');
                redirect(url('/auth/register'));
            }

            $contact = $updater->updateMissingFields($results[0]);

            HubSpotLogger::contactOperation('login_success', [
                'contact_id' => $contact['id'] ?? null,
                'ncode' => $nationalCode,
            ]);

            $_SESSION['login_state'] = 1;
            $_SESSION['user_role'] = 'User';
            $_SESSION['contact'] = $contact;

            redirect(url('/user/panel'));
        } catch (\Throwable $e) {
            HubSpotLogger::requestFailed(
                'login',
                'POST',
                '/crm/v3/objects/contacts/search',
                0,
                null,
                '',
                microtime(true),
                $e,
                ['ncode' => $nationalCode],
            );

            remember_old($request->all());
            set_errors(['national_code' => ['خطا در ارتباط با سرور. لطفاً دوباره تلاش کنید.']]);
            redirect(url('/auth/login'));
        }
    }

    public static function logout(): never
    {
        session_destroy();
        session_start();
        redirect(url('/auth/login'));
    }
}
