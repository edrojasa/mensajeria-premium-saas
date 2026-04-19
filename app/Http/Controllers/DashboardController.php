<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Shipments\ShipmentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $today = Carbon::today();

        $base = Shipment::query();

        if ($request->user()->isMessenger()) {
            $base->where('assigned_user_id', $request->user()->id);
        }

        $registeredToday = (clone $base)
            ->whereDate('created_at', $today)
            ->count();

        $inTransit = (clone $base)
            ->whereIn('status', [
                ShipmentStatus::RECEIVED,
                ShipmentStatus::IN_TRANSIT,
                ShipmentStatus::OUT_FOR_DELIVERY,
            ])
            ->count();

        $deliveredToday = (clone $base)
            ->where('status', ShipmentStatus::DELIVERED)
            ->whereDate('updated_at', $today)
            ->count();

        $incidents = (clone $base)
            ->where('status', ShipmentStatus::INCIDENT)
            ->count();

        return view('dashboard', [
            'metrics' => [
                'registered_today' => $registeredToday,
                'in_transit' => $inTransit,
                'delivered_today' => $deliveredToday,
                'incidents' => $incidents,
            ],
            'courierDashboard' => $request->user()->isMessenger(),
        ]);
    }
}
