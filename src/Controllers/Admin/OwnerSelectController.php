<?php

namespace App\Controllers\Admin;

use App\Http\Request;
use App\Services\HubSpot\HubSpotClient;
use App\Services\HubSpot\OwnerService;

class OwnerSelectController
{
    public static function show(): string
    {
        $ownerList = [];

        try {
            $response = (new OwnerService(new HubSpotClient()))->list();
            $ownerList = $response['results'] ?? [];
        } catch (\Throwable) {
            //
        }

        return view('admin.owner-select', [
            'owners' => $ownerList,
            'selectedOwner' => session('admin_owner'),
        ]);
    }

    public static function store(Request $request): never
    {
        $data = $request->validate(['owner_id' => 'required|string']);

        [$ownerId, $ownerName] = array_pad(explode('|', $data['owner_id'], 2), 2, 'Owner');

        $_SESSION['admin_owner'] = [
            'id' => $ownerId,
            'name' => $ownerName ?: 'Owner',
        ];

        redirect(url('/admin/dashboard'));
    }
}
