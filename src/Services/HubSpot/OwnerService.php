<?php

namespace App\Services\HubSpot;

class OwnerService
{
    public function __construct(private readonly HubSpotClient $hubSpot) {}

    public function list(): array
    {
        $response = $this->hubSpot->request('owner.list', 'GET', '/crm/v3/owners');
        $this->hubSpot->throwIfFailed($response, 'HubSpot owner list failed');

        return $response['body'] ?? [];
    }
}
