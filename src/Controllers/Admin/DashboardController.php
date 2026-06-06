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
            'won_deals' => 0,
            'lost_deals' => 0,
            'owner' => session('admin_owner'),
        ];

        try {
            $contacts = new ContactService(new HubSpotClient());
            $deals = new DealService(new HubSpotClient());

            $contactList = $contacts->list(100);
            $dealList = $deals->list(100);

            $allContacts = $contactList['results'] ?? [];
            $allDeals = $dealList['results'] ?? [];

            $stats['contacts'] = count($allContacts);
            $stats['deals'] = count($allDeals);

            foreach ($allDeals as $deal) {
                $stage = strtolower($deal['properties']['dealstage'] ?? '');
                if (str_contains($stage, 'won') || str_contains($stage, 'closed')) {
                    $stats['won_deals']++;
                } elseif (str_contains($stage, 'lost')) {
                    $stats['lost_deals']++;
                }
            }
        } catch (\Throwable) {
            //
        }

        return view('admin.dashboard', compact('stats'));
    }
}
