<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourierShipmentController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = request()->user();

        if (! $user->isMessenger()) {
            return redirect()->route('shipments.index');
        }

        $shipments = Shipment::query()
            ->where('assigned_user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('courier.shipments.index', compact('shipments'));
    }
}
