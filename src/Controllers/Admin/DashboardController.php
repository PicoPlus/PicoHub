<?php

namespace App\Controllers\Admin;

use App\Services\HubSpot\ContactService;
use App\Services\HubSpot\DealService;
use App\Services\HubSpot\HubSpotClient;

class DashboardController
{
    public static function index(): string
    {
        $stats = [
            'contacts' => 0,
            'deals' => 0,
            'owner' => session('admin_owner'),
        ];

        try {
            $contacts = new ContactService(new HubSpotClient());
            $deals = new DealService(new HubSpotClient());

            $contactList = $contacts->list(10);
            $dealList = $deals->list(10);
            $stats['contacts'] = count($contactList['results'] ?? []);
            $stats['deals'] = count($dealList['results'] ?? []);
        } catch (\Throwable) {
            //
        }

        return view('admin.dashboard', compact('stats'));
    }
}
