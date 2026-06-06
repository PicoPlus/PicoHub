<?php

namespace App\Controllers\Admin;

use App\Services\HubSpot\ContactService;
use App\Services\HubSpot\HubSpotClient;

class UsersController
{
    public static function index(): string
    {
        $adminUsers = config('admin_users');
        $currentAdmin = $_SESSION['admin_email'] ?? '';

        $recentContacts = [];
        $error = null;

        try {
            $contactService = new ContactService(new HubSpotClient());
            $result = $contactService->list(20);
            $recentContacts = $result['results'] ?? [];
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return view('admin.pages.users', compact('adminUsers', 'currentAdmin', 'recentContacts', 'error'));
    }
}
