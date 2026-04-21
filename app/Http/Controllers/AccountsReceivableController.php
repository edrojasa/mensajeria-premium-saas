<?php

namespace App\Http\Controllers;

use App\Exports\AccountsReceivableExport;
use App\Finance\PaymentStatus;
use App\Models\Shipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class AccountsReceivableController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()?->canAccessFinancialModule(), 403);
        $orgId = tenant_id();
        if ($orgId === null) {
            abort(403);
        }

        $shipments = Shipment::query()
            ->with(['customer:id,name,customer_code'])
            ->where('organization_id', $orgId)
            ->where('payment_status', PaymentStatus::PENDING)
            ->orderByDesc('created_at')
            ->get();

        $grouped = $shipments->groupBy(fn (Shipment $s) => $s->customer_id ? 'customer:'.$s->customer_id : 'no-customer');
        $groups = $grouped->map(function ($rows, $key) {
            /** @var \Illuminate\Support\Collection<int, Shipment> $rows */
            $first = $rows->first();
            $customer = str_starts_with((string) $key, 'customer:') ? $first?->customer : null;

            return [
                'key' => $key,
                'customer_name' => $customer?->name ?? __('finance.unknown_customer_group'),
                'customer_code' => $customer?->customer_code ?? null,
                'shipments_count' => $rows->count(),
                'total_due' => (float) $rows->sum(fn (Shipment $s) => $s->balanceDue()),
                'rows' => $rows->sortByDesc('created_at')->values(),
            ];
        })->sortByDesc('total_due')->values();

        return view('financial.receivables', [
            'groups' => $groups,
            'totalGeneral' => (float) $groups->sum('total_due'),
            'totalNoCustomer' => (float) $groups->where('key', 'no-customer')->sum('total_due'),
            'rowsNoCustomer' => (int) $groups->where('key', 'no-customer')->sum('shipments_count'),
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        abort_unless($request->user()?->canAccessFinancialModule(), 403);

        $view = $this->__invoke($request)->getData();

        $pdf = Pdf::loadView('financial.pdf.receivables', [
            'groups' => $view['groups'],
            'totalGeneral' => $view['totalGeneral'],
            'totalNoCustomer' => $view['totalNoCustomer'],
            'title' => __('finance.receivables_pdf_title'),
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('cartera-'.now()->format('Y-m-d_His').'.pdf');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        abort_unless($request->user()?->canAccessFinancialModule(), 403);
        $orgId = tenant_id();
        if ($orgId === null) {
            abort(403);
        }

        $rows = Shipment::query()
            ->with(['customer:id,name,customer_code'])
            ->where('organization_id', $orgId)
            ->where('payment_status', PaymentStatus::PENDING)
            ->orderByDesc('created_at');

        return Excel::download(
            new AccountsReceivableExport($rows),
            'cartera-'.now()->format('Y-m-d_His').'.xlsx'
        );
    }
}
