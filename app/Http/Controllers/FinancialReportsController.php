<?php

namespace App\Http\Controllers;

use App\Exports\FinancialMovementsExport;
use App\Finance\PaymentStatus;
use App\Models\Shipment;
use App\Models\User;
use App\Support\FinancialMovements;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class FinancialReportsController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()?->canAccessFinancialModule(), 403);
        $orgId = tenant_id();
        if ($orgId === null) {
            abort(403);
        }

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $base = Shipment::query()->where('organization_id', $orgId);

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
                ->where('organization_id', $orgId)
                ->whereBetween('created_at', [$start, $end])
                ->sum('cost');

            $chartPaid[] = (float) Shipment::query()
                ->where('organization_id', $orgId)
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
            'movements' => FinancialMovements::paginated($request, null, 'movements_page'),
            'customers' => \App\Models\Customer::query()->active()->orderBy('name')->get(['id', 'name']),
            'users' => $this->tenantUsers(),
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        abort_unless($request->user()?->canAccessFinancialModule(), 403);

        $rows = FinancialMovements::mapShipmentsToMovements(
            FinancialMovements::filteredShipmentsQuery($request)->get()
        );

        $pdf = Pdf::loadView('financial.pdf.report-movements', [
            'title' => __('finance.reports_title'),
            'generatedAt' => now(),
            'rows' => $rows,
            'total' => (float) $rows->sum('value'),
            'paidTotal' => (float) $rows->where('status', PaymentStatus::PAID)->sum('value'),
            'pendingTotal' => (float) $rows->where('status', PaymentStatus::PENDING)->sum('value'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('reporte-financiero-'.now()->format('Y-m-d_His').'.pdf');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        abort_unless($request->user()?->canAccessFinancialModule(), 403);

        $rows = FinancialMovements::mapShipmentsToMovements(
            FinancialMovements::filteredShipmentsQuery($request)->get()
        );

        return Excel::download(
            new FinancialMovementsExport($rows),
            'reporte-financiero-'.now()->format('Y-m-d_His').'.xlsx'
        );
    }

    private function tenantUsers()
    {
        $orgId = tenant_id();
        if ($orgId === null) {
            return collect();
        }

        return User::query()
            ->join('organization_user', 'users.id', '=', 'organization_user.user_id')
            ->where('organization_user.organization_id', $orgId)
            ->where('organization_user.is_active', true)
            ->select('users.id', 'users.name')
            ->orderBy('users.name')
            ->get();
    }
}
