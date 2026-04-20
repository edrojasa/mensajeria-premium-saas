<?php

namespace App\Http\Controllers;

use App\Finance\PaymentStatus;
use App\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialReportsController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()?->canAccessFinancialModule(), 403);

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $base = Shipment::query();

        $billedMonth = (clone $base)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('cost');

        $paidMonth = (clone $base)
            ->where('payment_status', PaymentStatus::PAID)
            ->where(function ($q) use ($monthStart, $monthEnd): void {
                $q->whereBetween('payment_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                    ->orWhere(function ($q2) use ($monthStart, $monthEnd): void {
                        $q2->whereNull('payment_date')
                            ->whereBetween('updated_at', [$monthStart, $monthEnd]);
                    });
            })
            ->sum('paid_amount');

        $pendingPortfolio = (clone $base)
            ->where('payment_status', PaymentStatus::PENDING)
            ->get()
            ->reduce(function (float $carry, Shipment $s): float {
                return $carry + $s->balanceDue();
            }, 0.0);

        $chartLabels = [];
        $chartBilled = [];
        $chartPaid = [];

        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = Carbon::now()->subMonths($i)->endOfMonth();
            $chartLabels[] = $start->format('m/Y');

            $chartBilled[] = (float) Shipment::query()
                ->whereBetween('created_at', [$start, $end])
                ->sum('cost');

            $chartPaid[] = (float) Shipment::query()
                ->where('payment_status', PaymentStatus::PAID)
                ->where(function ($q) use ($start, $end): void {
                    $q->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
                        ->orWhere(function ($q2) use ($start, $end): void {
                            $q2->whereNull('payment_date')
                                ->whereBetween('updated_at', [$start, $end]);
                        });
                })
                ->sum('paid_amount');
        }

        return view('financial.reports', [
            'billedMonth' => $billedMonth,
            'paidMonth' => $paidMonth,
            'pendingPortfolio' => $pendingPortfolio,
            'chartLabels' => $chartLabels,
            'chartBilled' => $chartBilled,
            'chartPaid' => $chartPaid,
        ]);
    }
}
