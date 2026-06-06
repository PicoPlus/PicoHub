<?php

namespace App\Controllers\Admin;

use App\Services\HubSpot\ContactService;
use App\Services\HubSpot\DealService;
use App\Services\HubSpot\HubSpotClient;

class AdminPageController
{
    public static function kanban(): string
    {
        $deals = [];

        try {
            $dealService = new DealService(new HubSpotClient());
            $result = $dealService->list(100);
            $deals = $result['results'] ?? [];
        } catch (\Throwable) {
            //
        }

        return view('admin.pages.kanban', compact('deals'));
    }

    public static function contacts(): string
    {
        $contacts = [];

        try {
            $contactService = new ContactService(new HubSpotClient());
            $result = $contactService->list(100);
            $contacts = $result['results'] ?? [];
        } catch (\Throwable) {
            //
        }

        return view('admin.pages.contacts', compact('contacts'));
    }

    public static function deals(): string
    {
        $deals = [];

        try {
            $dealService = new DealService(new HubSpotClient());
            $result = $dealService->list(100);
            $deals = $result['results'] ?? [];
        } catch (\Throwable) {
            //
        }

        return view('admin.pages.deals', compact('deals'));
    }

    public static function tickets(): string
    {
        $tickets = [];

        try {
            $client = new HubSpotClient();
            $response = $client->request('tickets.list', 'GET', '/crm/v3/objects/tickets', [
                'query' => ['limit' => 100],
            ]);

            if ($response['status'] >= 200 && $response['status'] < 300) {
                $tickets = $response['body']['results'] ?? [];
            }
        } catch (\Throwable) {
            //
        }

        return view('admin.pages.tickets', compact('tickets'));
    }

    public static function analytics(): string
    {
        $stats = [
            'contacts' => 0,
            'deals' => 0,
            'won_deals' => 0,
            'lost_deals' => 0,
        ];

        try {
            $contactService = new ContactService(new HubSpotClient());
            $dealService = new DealService(new HubSpotClient());

            $contactResult = $contactService->list(100);
            $dealResult = $dealService->list(100);

            $contacts = $contactResult['results'] ?? [];
            $deals = $dealResult['results'] ?? [];

            $stats['contacts'] = count($contacts);
            $stats['deals'] = count($deals);

            foreach ($deals as $deal) {
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

        return view('admin.pages.analytics', compact('stats'));
    }

    public static function settings(): string
    {
        $hubspotToken = config('hubspot.token');
        $hubspot_connected = $hubspotToken !== '';
        $hubspot_token_masked = $hubspotToken !== ''
            ? substr($hubspotToken, 0, 4) . '••••' . substr($hubspotToken, -4)
            : '';

        $sms_configured = config('ippanel.api_key') !== '';
        $zohal_configured = config('zohal.token') !== '';

        return view('admin.pages.settings', compact(
            'hubspot_connected',
            'hubspot_token_masked',
            'sms_configured',
            'zohal_configured',
        ));
    }
}
