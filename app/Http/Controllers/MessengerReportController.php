<?php

namespace App\Http\Controllers;

use App\Exports\MessengerReportExport;
use App\Support\MessengerReportBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class MessengerReportController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->canOperateLogistics(), 403);

        $summary = MessengerReportBuilder::messengersSummary($request);
        $selectedMessengerId = (int) ($request->query('messenger_id') ?: ($summary->first()['id'] ?? 0));

        return view('operations.messengers-report', [
            'summaryRows' => $summary,
            'globalMetrics' => MessengerReportBuilder::metrics($request),
            'detailMetrics' => $selectedMessengerId > 0
                ? MessengerReportBuilder::detailMetrics($request, $selectedMessengerId)
                : null,
            'history' => MessengerReportBuilder::history($request),
            'statusChart' => $selectedMessengerId > 0 ? MessengerReportBuilder::statusChart($request, $selectedMessengerId) : ['labels' => [], 'data' => []],
            'incomeChart' => $selectedMessengerId > 0 ? MessengerReportBuilder::incomeByDateChart($request, $selectedMessengerId) : ['labels' => [], 'data' => []],
            'deliveriesChart' => $selectedMessengerId > 0 ? MessengerReportBuilder::deliveredEvolutionChart($request, $selectedMessengerId) : ['labels' => [], 'data' => []],
            'selectedMessengerId' => $selectedMessengerId,
            'customers' => \App\Models\Customer::query()->active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        abort_unless($request->user()?->canOperateLogistics(), 403);

        $history = MessengerReportBuilder::history($request)->getCollection();
        $summary = MessengerReportBuilder::messengersSummary($request);

        $pdf = Pdf::loadView('operations.pdf.messengers-report', [
            'title' => 'Reporte de Mensajeros',
            'generatedAt' => now(),
            'summaryRows' => $summary,
            'globalMetrics' => MessengerReportBuilder::metrics($request),
            'history' => $history,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('reporte-mensajeros-'.now()->format('Y-m-d_His').'.pdf');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        abort_unless($request->user()?->canOperateLogistics(), 403);

        $rows = MessengerReportBuilder::history($request)->getCollection()->map(function ($shipment): array {
            return [
                'fecha' => optional($shipment->created_at)->timezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                'mensajero' => $shipment->assignedCourier?->name ?? '—',
                'envio' => $shipment->tracking_number,
                'cliente' => $shipment->customer?->name ?? __('finance.unknown_customer_group'),
                'estado' => $shipment->status,
                'valor' => (float) ($shipment->cost ?? 0),
                'estado_pago' => $shipment->payment_status,
            ];
        });

        return Excel::download(
            new MessengerReportExport($rows),
            'reporte-mensajeros-'.now()->format('Y-m-d_His').'.xlsx'
        );
    }
}
