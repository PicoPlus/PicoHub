<?php

namespace App\Controllers\Deal;

use App\Http\Request;
use App\Services\HubSpot\ContactService;
use App\Services\HubSpot\DealService;
use App\Services\HubSpot\HubSpotClient;
use App\Support\NationalCodeValidator;

class SearchController
{
    public static function show(): string
    {
        return view('deal.search', ['deals' => [], 'query' => '']);
    }

    public static function search(Request $request): string
    {
        $data = $request->validate(['national_code' => 'required|string|size:10']);

        $nationalCode = trim($data['national_code']);
        $dealsList = [];

        if (NationalCodeValidator::validate($nationalCode) === null) {
            try {
                $contacts = new ContactService(new HubSpotClient());
                $deals = new DealService(new HubSpotClient());

                $contactSearch = $contacts->searchByNationalCode($nationalCode);

                if (!empty($contactSearch['results'][0]['id'])) {
                    $contact = $contacts->read($contactSearch['results'][0]['id']);
                    $associations = $contact['associations']['deals']['results'] ?? [];

                    foreach ($associations as $assoc) {
                        try {
                            $dealsList[] = $deals->read($assoc['id']);
                        } catch (\Throwable) {
                            continue;
                        }
                    }
                }
            } catch (\Throwable) {
                //
            }
        }

        return view('deal.search', [
            'deals' => $dealsList,
            'query' => $nationalCode,
        ]);
    }
}
