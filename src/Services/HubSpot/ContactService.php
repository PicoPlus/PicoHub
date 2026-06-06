<?php

namespace App\Services\HubSpot;

class ContactService
{
    private const BASE = '/crm/v3/objects/contacts';

    public function __construct(private readonly HubSpotClient $hubSpot) {}

    public function searchByProperty(string $property, string $value, ?array $properties = null): array
    {
        $properties ??= config('contact_properties');

        HubSpotLogger::contactOperation('search_by_property', [
            'property' => $property,
            'value' => $value,
            'requested_properties' => $properties,
        ]);

        $response = $this->hubSpot->request('contact.search', 'POST', self::BASE . '/search', [
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
        ], [
            'contact_property' => $property,
            'search_value' => $value,
        ]);

        $this->hubSpot->throwIfFailed($response, 'HubSpot contact search failed');

        return $response['body'] ?? [];
    }

    public function searchByNationalCode(string $nationalCode): array
    {
        return $this->searchByProperty('ncode', $nationalCode);
    }

    public function read(string $id, ?array $properties = null): array
    {
        $properties ??= config('contact_properties');

        $query = [];
        foreach (array_values($properties) as $index => $property) {
            $query["properties[{$index}]"] = $property;
        }
        $query['associations[0]'] = 'deals';

        HubSpotLogger::contactOperation('read', [
            'contact_id' => $id,
            'requested_properties' => $properties,
        ]);

        $response = $this->hubSpot->request('contact.read', 'GET', self::BASE . '/' . $id, [
            'query' => $query,
        ], [
            'contact_id' => $id,
        ]);

        $this->hubSpot->throwIfFailed($response, 'HubSpot contact read failed');

        return $response['body'] ?? [];
    }

    public function create(array $properties): array
    {
        $mapped = $this->mapContactProperties($properties);

        HubSpotLogger::contactOperation('create', [
            'properties' => $mapped,
        ]);

        $response = $this->hubSpot->request('contact.create', 'POST', self::BASE, [
            'json' => ['properties' => $mapped],
        ], [
            'properties' => $mapped,
        ]);

        $this->hubSpot->throwIfFailed($response, 'HubSpot contact create failed');

        $body = $response['body'] ?? [];

        HubSpotLogger::contactOperation('create_success', [
            'contact_id' => $body['id'] ?? null,
            'ncode' => $mapped['ncode'] ?? null,
            'date_of_birth' => $mapped['date_of_birth'] ?? null,
            'father_name' => $mapped['father_name'] ?? null,
        ]);

        return $body;
    }

    public function updateProperties(string $contactId, array $properties): array
    {
        $mapped = $this->mapContactProperties($properties);

        HubSpotLogger::contactOperation('update', [
            'contact_id' => $contactId,
            'updates' => $mapped,
        ]);

        $response = $this->hubSpot->request('contact.update', 'PATCH', self::BASE . '/' . $contactId, [
            'json' => ['properties' => $mapped],
        ], [
            'contact_id' => $contactId,
            'updates' => $mapped,
        ]);

        $this->hubSpot->throwIfFailed($response, 'HubSpot contact update failed');

        $body = $response['body'] ?? [];

        HubSpotLogger::contactOperation('update_success', [
            'contact_id' => $contactId,
            'updated_fields' => array_keys($mapped),
        ]);

        return $body;
    }

    public function list(int $limit = 100, ?string $after = null): array
    {
        $query = ['limit' => $limit];
        if ($after) {
            $query['after'] = $after;
        }

        HubSpotLogger::contactOperation('list', ['limit' => $limit, 'after' => $after]);

        $response = $this->hubSpot->request('contact.list', 'GET', self::BASE, [
            'query' => $query,
        ]);

        $this->hubSpot->throwIfFailed($response, 'HubSpot contact list failed');

        return $response['body'] ?? [];
    }

    /**
     * Normalize incoming field names to HubSpot property names.
     */
    private function mapContactProperties(array $properties): array
    {
        $aliases = [
            'national_code' => 'ncode',
            'natcode' => 'ncode',
            'birth_date' => 'date_of_birth',
            'dateofbirth' => 'date_of_birth',
        ];

        $mapped = [];

        foreach ($properties as $key => $value) {
            $hubspotKey = $aliases[$key] ?? $key;
            $mapped[$hubspotKey] = $value;

            HubSpotLogger::propertyMapping($key, $hubspotKey, $value, [
                'direction' => 'outbound',
            ]);
        }

        return $mapped;
    }
}
