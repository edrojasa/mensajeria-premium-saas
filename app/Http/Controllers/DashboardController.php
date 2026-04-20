<?php

namespace App\Http\Controllers;

use App\Finance\PaymentStatus;
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

        $deliveredTotal = (clone $base)
            ->where('status', ShipmentStatus::DELIVERED)
            ->count();

        $incidents = (clone $base)
            ->where('status', ShipmentStatus::INCIDENT)
            ->count();

        $chartDailyLabels = [];
        $chartDailyCounts = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = $today->copy()->subDays($i);
            $chartDailyLabels[] = $day->format('d/m');
            $chartDailyCounts[] = (clone $base)
                ->whereDate('created_at', $day)
                ->count();
        }

        $financialMetrics = null;

        if ($request->user()->canAccessFinancialModule()) {
            $monthStart = Carbon::now()->startOfMonth();
            $monthEnd = Carbon::now()->endOfMonth();

            $fb = Shipment::query();

            $billedMonth = (clone $fb)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('cost');

            $paidMonth = (clone $fb)
                ->where('payment_status', PaymentStatus::PAID)
                ->where(function ($q) use ($monthStart, $monthEnd): void {
                    $q->whereBetween('payment_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                        ->orWhere(function ($q2) use ($monthStart, $monthEnd): void {
                            $q2->whereNull('payment_date')
                                ->whereBetween('updated_at', [$monthStart, $monthEnd]);
                        });
                })
                ->sum('paid_amount');

            $pendingBalance = Shipment::query()
                ->where('payment_status', PaymentStatus::PENDING)
                ->get()
                ->sum(fn (Shipment $s) => $s->balanceDue());

            $financialMetrics = [
                'billed_month' => (float) $billedMonth,
                'paid_month' => (float) $paidMonth,
                'pending_balance' => (float) $pendingBalance,
            ];
        }

        return view('dashboard', [
            'metrics' => [
                'registered_today' => $registeredToday,
                'in_transit' => $inTransit,
                'delivered_total' => $deliveredTotal,
                'incidents' => $incidents,
            ],
            'chartDailyLabels' => $chartDailyLabels,
            'chartDailyCounts' => $chartDailyCounts,
            'courierDashboard' => $request->user()->isMessenger(),
            'financialMetrics' => $financialMetrics,
        ]);
    }
}
