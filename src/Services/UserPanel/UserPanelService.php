<?php

namespace App\Services\UserPanel;

use App\Services\HubSpot\ContactService;
use App\Services\HubSpot\DealService;
use App\Support\FileCache;

class UserPanelService
{
    public function __construct(
        private readonly ContactService $contacts,
        private readonly DealService $deals,
    ) {}

    public function load(string $contactId): ?array
    {
        $cacheKey = 'user_panel_' . $contactId;
        $ttl = (int) config('cache.user_panel_ttl', 300);

        return FileCache::remember($cacheKey, $ttl, function () use ($contactId) {
            $contact = $this->contacts->read($contactId);
            $props = $contact['properties'] ?? [];

            $dealResults = [];
            $associations = $contact['associations']['deals']['results'] ?? [];

            foreach ($associations as $assoc) {
                try {
                    $dealResults[] = $this->deals->read($assoc['id']);
                } catch (\Throwable) {
                    continue;
                }
            }

            $openDeals = 0;
            $closedDeals = 0;
            foreach ($dealResults as $deal) {
                $stage = $deal['properties']['dealstage'] ?? '';
                if (str_contains(strtolower($stage), 'closed') || str_contains($stage, 'won')) {
                    $closedDeals++;
                } else {
                    $openDeals++;
                }
            }

            return [
                'contact' => $contact,
                'deals' => $dealResults,
                'stats' => [
                    'total_revenue' => $props['total_revenue'] ?? 0,
                    'wallet' => $props['wallet'] ?? 0,
                    'open_deals' => $openDeals,
                    'closed_deals' => $closedDeals,
                    'total_deals' => count($dealResults),
                    'contact_plan' => $props['contact_plan'] ?? '-',
                ],
            ];
        });
    }
}
