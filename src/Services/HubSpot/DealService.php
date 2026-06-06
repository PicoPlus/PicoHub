<?php

namespace App\Services\HubSpot;

class DealService
{
    private const BASE = '/crm/v3/objects/deals';

    public function __construct(private readonly HubSpotClient $hubSpot) {}

    public function searchByProperty(string $property, string $value, ?array $properties = null): array
    {
        $properties ??= ['dealname', 'dealstage', 'amount', 'closedate', 'pipeline', 'payment_type'];

        $response = $this->hubSpot->request('deal.search', 'POST', self::BASE . '/search', [
            'json' => [
                'limit' => 100,
                'properties' => $properties,
                'filterGroups' => [[
                    'filters' => [[
                        'propertyName' => $property,
                        'operator' => 'EQ',
                        'value' => $value,
                    ]],
                ]],
            ],
        ], ['deal_property' => $property]);

        $this->hubSpot->throwIfFailed($response, 'HubSpot deal search failed');

        return $response['body'] ?? [];
    }

    public function read(string $id): array
    {
        $response = $this->hubSpot->request('deal.read', 'GET', self::BASE . '/' . $id, [
            'query' => ['associations' => 'contacts,line_items'],
        ], ['deal_id' => $id]);

        $this->hubSpot->throwIfFailed($response, 'HubSpot deal read failed');

        return $response['body'] ?? [];
    }

    public function create(array $properties): array
    {
        $response = $this->hubSpot->request('deal.create', 'POST', self::BASE, [
            'json' => ['properties' => $properties],
        ]);

        $this->hubSpot->throwIfFailed($response, 'HubSpot deal create failed');

        return $response['body'] ?? [];
    }

    public function list(int $limit = 100): array
    {
        $response = $this->hubSpot->request('deal.list', 'GET', self::BASE, [
            'query' => ['limit' => $limit],
        ]);

        $this->hubSpot->throwIfFailed($response, 'HubSpot deal list failed');

        return $response['body'] ?? [];
    }
}
