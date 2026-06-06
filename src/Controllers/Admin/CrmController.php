<?php

namespace App\Controllers\Admin;

use App\Services\HubSpot\ContactService;
use App\Services\HubSpot\DealService;
use App\Services\HubSpot\HubSpotClient;

class CrmController
{
    private static function client(): HubSpotClient
    {
        return new HubSpotClient();
    }

    // ─── Companies ─────────────────────────────────────────────
    public static function companies(): string
    {
        $companies = [];
        $error = null;

        try {
            $client = self::client();
            $response = $client->request('companies.list', 'GET', '/crm/v3/objects/companies', [
                'query' => ['limit' => 100, 'properties' => 'name,domain,phone,city,industry'],
            ]);
            if ($response['status'] >= 200 && $response['status'] < 300) {
                $companies = $response['body']['results'] ?? [];
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return view('admin.pages.companies', compact('companies', 'error'));
    }

    public static function companyCreate(): string
    {
        $name = trim($_POST['name'] ?? '');
        $domain = trim($_POST['domain'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $industry = trim($_POST['industry'] ?? '');

        if ($name === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'نام شرکت الزامی است.'];
            return redirect(url('/admin/companies'));
        }

        try {
            $client = self::client();
            $properties = array_filter(['name' => $name, 'domain' => $domain, 'phone' => $phone, 'industry' => $industry]);
            $response = $client->request('company.create', 'POST', '/crm/v3/objects/companies', [
                'json' => ['properties' => $properties],
            ]);

            if ($response['status'] >= 200 && $response['status'] < 300) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'شرکت با موفقیت ایجاد شد.'];
            } else {
                $msg = $response['body']['message'] ?? 'خطای ناشناخته';
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا: ' . $msg];
            }
        } catch (\Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا: ' . $e->getMessage()];
        }

        return redirect(url('/admin/companies'));
    }

    public static function companyDelete(): string
    {
        $id = trim($_POST['id'] ?? '');
        if ($id === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'شناسه شرکت نامعتبر.'];
            return redirect(url('/admin/companies'));
        }

        try {
            $client = self::client();
            $response = $client->request('company.delete', 'DELETE', '/crm/v3/objects/companies/' . $id);
            if ($response['status'] >= 200 && $response['status'] < 300) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'شرکت حذف شد.'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا در حذف شرکت.'];
            }
        } catch (\Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا: ' . $e->getMessage()];
        }

        return redirect(url('/admin/companies'));
    }

    // ─── Contacts CRUD ─────────────────────────────────────────
    public static function contactDelete(): string
    {
        $id = trim($_POST['id'] ?? '');
        if ($id === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'شناسه مخاطب نامعتبر.'];
            return redirect(url('/admin/contacts'));
        }

        try {
            $client = self::client();
            $response = $client->request('contact.delete', 'DELETE', '/crm/v3/objects/contacts/' . $id);
            if ($response['status'] >= 200 && $response['status'] < 300) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'مخاطب حذف شد.'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا در حذف مخاطب.'];
            }
        } catch (\Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا: ' . $e->getMessage()];
        }

        return redirect(url('/admin/contacts'));
    }

    public static function contactUpdate(): string
    {
        $id = trim($_POST['id'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');

        if ($id === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'شناسه مخاطب نامعتبر.'];
            return redirect(url('/admin/contacts'));
        }

        try {
            $contactService = new ContactService(self::client());
            $properties = array_filter([
                'email' => $email,
                'phone' => $phone,
                'firstname' => $firstname,
                'lastname' => $lastname,
            ]);
            $contactService->updateProperties($id, $properties);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'مخاطب بروزرسانی شد.'];
        } catch (\Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا: ' . $e->getMessage()];
        }

        return redirect(url('/admin/contacts'));
    }

    // ─── Deals CRUD ────────────────────────────────────────────
    public static function dealCreate(): string
    {
        $dealname = trim($_POST['dealname'] ?? '');
        $amount = trim($_POST['amount'] ?? '');
        $pipeline = trim($_POST['pipeline'] ?? 'default');
        $dealstage = trim($_POST['dealstage'] ?? 'appointmentscheduled');

        if ($dealname === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'نام معامله الزامی است.'];
            return redirect(url('/admin/deals'));
        }

        try {
            $dealService = new DealService(self::client());
            $properties = array_filter([
                'dealname' => $dealname,
                'amount' => $amount,
                'pipeline' => $pipeline,
                'dealstage' => $dealstage,
            ]);
            $dealService->create($properties);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'معامله با موفقیت ایجاد شد.'];
        } catch (\Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا: ' . $e->getMessage()];
        }

        return redirect(url('/admin/deals'));
    }

    public static function dealDelete(): string
    {
        $id = trim($_POST['id'] ?? '');
        if ($id === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'شناسه معامله نامعتبر.'];
            return redirect(url('/admin/deals'));
        }

        try {
            $client = self::client();
            $response = $client->request('deal.delete', 'DELETE', '/crm/v3/objects/deals/' . $id);
            if ($response['status'] >= 200 && $response['status'] < 300) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'معامله حذف شد.'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا در حذف معامله.'];
            }
        } catch (\Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا: ' . $e->getMessage()];
        }

        return redirect(url('/admin/deals'));
    }

    // ─── Tickets CRUD ──────────────────────────────────────────
    public static function ticketCreate(): string
    {
        $subject = trim($_POST['subject'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $priority = trim($_POST['hs_ticket_priority'] ?? 'MEDIUM');

        if ($subject === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'موضوع تیکت الزامی است.'];
            return redirect(url('/admin/tickets'));
        }

        try {
            $client = self::client();
            $response = $client->request('ticket.create', 'POST', '/crm/v3/objects/tickets', [
                'json' => ['properties' => [
                    'subject' => $subject,
                    'content' => $content,
                    'hs_ticket_priority' => $priority,
                    'hs_pipeline_stage' => '1',
                ]],
            ]);

            if ($response['status'] >= 200 && $response['status'] < 300) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'تیکت با موفقیت ایجاد شد.'];
            } else {
                $msg = $response['body']['message'] ?? 'خطای ناشناخته';
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا: ' . $msg];
            }
        } catch (\Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا: ' . $e->getMessage()];
        }

        return redirect(url('/admin/tickets'));
    }

    public static function ticketDelete(): string
    {
        $id = trim($_POST['id'] ?? '');
        if ($id === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'شناسه تیکت نامعتبر.'];
            return redirect(url('/admin/tickets'));
        }

        try {
            $client = self::client();
            $response = $client->request('ticket.delete', 'DELETE', '/crm/v3/objects/tickets/' . $id);
            if ($response['status'] >= 200 && $response['status'] < 300) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'تیکت حذف شد.'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا در حذف تیکت.'];
            }
        } catch (\Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'خطا: ' . $e->getMessage()];
        }

        return redirect(url('/admin/tickets'));
    }
}
