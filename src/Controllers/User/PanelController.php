<?php

namespace App\Controllers\User;

use App\Services\HubSpot\ContactService;
use App\Services\HubSpot\DealService;
use App\Services\HubSpot\HubSpotClient;
use App\Services\UserPanel\UserPanelService;
use App\Support\FormatHelper;

class PanelController
{
    public static function index(): string
    {
        $contact = session('contact', []);
        $contactId = $contact['id'] ?? null;

        $userPanel = new UserPanelService(
            new ContactService(new HubSpotClient()),
            new DealService(new HubSpotClient()),
        );

        $panel = $contactId ? $userPanel->load($contactId) : null;
        $props = $panel['contact']['properties'] ?? $contact['properties'] ?? [];

        return view('user.panel', [
            'panel' => $panel,
            'fullName' => trim(($props['firstname'] ?? '') . ' ' . ($props['lastname'] ?? '')),
            'phone' => $props['phone'] ?? '-',
            'formatNumber' => static fn ($v) => FormatHelper::formatNumber($v),
        ]);
    }
}
