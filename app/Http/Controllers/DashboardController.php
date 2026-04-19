<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Shipments\ShipmentStatus;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = Carbon::today();

        $registeredToday = Shipment::query()
            ->whereDate('created_at', $today)
            ->count();

        $inTransit = Shipment::query()
            ->whereIn('status', [
                ShipmentStatus::RECEIVED,
                ShipmentStatus::IN_TRANSIT,
                ShipmentStatus::OUT_FOR_DELIVERY,
            ])
            ->count();

        $deliveredToday = Shipment::query()
            ->where('status', ShipmentStatus::DELIVERED)
            ->whereDate('updated_at', $today)
            ->count();

        $incidents = Shipment::query()
            ->where('status', ShipmentStatus::INCIDENT)
            ->count();

        return view('dashboard', [
            'metrics' => [
                'registered_today' => $registeredToday,
                'in_transit' => $inTransit,
                'delivered_today' => $deliveredToday,
                'incidents' => $incidents,
            ],
        ]);
    }
}
