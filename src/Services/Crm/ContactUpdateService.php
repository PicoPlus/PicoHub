<?php

namespace App\Services\Crm;

use App\Services\HubSpot\ContactService;
use App\Services\HubSpot\HubSpotLogger;
use App\Services\Zohal\ZohalService;
use App\Support\Logger;

class ContactUpdateService
{
    public function __construct(
        private readonly ContactService $contacts,
        private readonly ZohalService $zohal,
    ) {}

    public function updateMissingFields(array $contact): array
    {
        $props = $contact['properties'] ?? [];
        $contactId = $contact['id'] ?? null;

        if (!$contactId) {
            return $contact;
        }

        $ncode = $props['ncode'] ?? $props['natcode'] ?? '';
        $birthDate = $props['date_of_birth'] ?? $props['dateofbirth'] ?? '';
        $updates = [];

        HubSpotLogger::contactOperation('refresh_missing_fields_check', [
            'contact_id' => $contactId,
            'ncode_present' => $ncode !== '',
            'date_of_birth_present' => $birthDate !== '',
            'father_name_present' => !empty($props['father_name']),
            'gender_present' => !empty($props['gender']),
        ]);

        if ($ncode && $birthDate && (empty($props['father_name']) || empty($props['gender']))) {
            try {
                $inquiry = $this->zohal->nationalIdentityInquiry($ncode, $birthDate);

                HubSpotLogger::contactOperation('zohal_identity_refresh_result', [
                    'contact_id' => $contactId,
                    'success' => $inquiry['success'],
                    'matched' => $inquiry['matched'],
                ]);

                if ($inquiry['success'] && $inquiry['matched']) {
                    $data = $inquiry['data'];

                    if (empty($props['father_name']) && !empty($data['father_name'])) {
                        $updates['father_name'] = $data['father_name'];
                    }
                    if (empty($props['gender']) && $data['gender'] !== null && $data['gender'] !== '') {
                        $updates['gender'] = (string) $data['gender'];
                    }
                }
            } catch (\Throwable $e) {
                Logger::warning('app', 'Zohal identity refresh failed', [
                    'contact_id' => $contactId,
                    'ncode' => $ncode,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($updates !== []) {
            HubSpotLogger::contactOperation('refresh_missing_fields_apply', [
                'contact_id' => $contactId,
                'updates' => $updates,
            ]);

            $this->contacts->updateProperties($contactId, $updates);
            $contact = $this->contacts->read($contactId);
        } else {
            HubSpotLogger::contactOperation('refresh_missing_fields_skip', [
                'contact_id' => $contactId,
                'reason' => 'no_updates_needed',
            ]);
        }

        return $contact;
    }
}
