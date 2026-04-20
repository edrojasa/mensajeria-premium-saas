<?php

namespace App\Http\Controllers;

use App\Exports\ActivityLogsExport;
use App\Exports\CustomersDetailedExport;
use App\Exports\OrganizationUsersExport;
use App\Exports\ShipmentsExport;
use App\Support\ActivityLogsListing;
use App\Support\CustomersListing;
use App\Support\CustomersWithShipmentsExportBuilder;
use App\Support\OrganizationUsersListing;
use App\Support\ShipmentsListing;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class DataExportController extends Controller
{
    public function shipmentsExcel(Request $request): BinaryFileResponse
    {
        $this->authorizeExport($request);

        $query = ShipmentsListing::filteredQuery($request);

        return Excel::download(
            new ShipmentsExport($query),
            'envios-'.now()->format('Y-m-d_His').'.xlsx'
        );
    }

    public function shipmentsPdf(Request $request): Response
    {
        $this->authorizeExport($request);

        $rows = ShipmentsListing::filteredQuery($request)->get();

        $pdf = Pdf::loadView('exports.pdf.shipments', [
            'rows' => $rows,
            'title' => __('exports.pdf_shipments_title'),
            'generatedAt' => now()->timezone(config('app.timezone')),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('envios-'.now()->format('Y-m-d_His').'.pdf');
    }

    public function customersExcel(Request $request): BinaryFileResponse
    {
        $this->authorizeExport($request);

        $query = CustomersListing::filteredQuery($request);
        $rows = CustomersWithShipmentsExportBuilder::rowsForListingQuery($query);

        return Excel::download(
            new CustomersDetailedExport($rows),
            'clientes-'.now()->format('Y-m-d_His').'.xlsx'
        );
    }

    public function customersPdf(Request $request): Response
    {
        $this->authorizeExport($request);

        $customers = CustomersListing::filteredQuery($request)
            ->with([
                'shipments' => fn ($q) => $q->with('assignedCourier')->latest(),
            ])
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('exports.pdf.customers-detailed', [
            'customers' => $customers,
            'title' => __('exports.pdf_customers_title'),
            'generatedAt' => now()->timezone(config('app.timezone')),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('clientes-'.now()->format('Y-m-d_His').'.pdf');
    }

    public function usersExcel(Request $request): BinaryFileResponse
    {
        $this->authorizeExport($request);

        $orgId = $this->tenantOrganizationId();
        $query = OrganizationUsersListing::filteredQuery($request, $orgId);

        return Excel::download(
            new OrganizationUsersExport($query, $orgId),
            'usuarios-'.now()->format('Y-m-d_His').'.xlsx'
        );
    }

    public function usersPdf(Request $request): Response
    {
        $this->authorizeExport($request);

        $orgId = $this->tenantOrganizationId();
        $rows = OrganizationUsersListing::filteredQuery($request, $orgId)->get();

        $pdf = Pdf::loadView('exports.pdf.users', [
            'rows' => $rows,
            'organizationId' => $orgId,
            'title' => __('exports.pdf_users_title'),
            'generatedAt' => now()->timezone(config('app.timezone')),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('usuarios-'.now()->format('Y-m-d_His').'.pdf');
    }

    public function logsExcel(Request $request): BinaryFileResponse
    {
        $this->authorizeAuditLogsExport($request);

        $query = ActivityLogsListing::filteredQuery($request);

        return Excel::download(
            new ActivityLogsExport($query),
            'auditoria-'.now()->format('Y-m-d_His').'.xlsx'
        );
    }

    public function logsPdf(Request $request): Response
    {
        $this->authorizeAuditLogsExport($request);

        $rows = ActivityLogsListing::filteredQuery($request)->get();

        $pdf = Pdf::loadView('exports.pdf.activity-logs', [
            'rows' => $rows,
            'title' => __('exports.pdf_logs_title'),
            'generatedAt' => now()->timezone(config('app.timezone')),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('auditoria-'.now()->format('Y-m-d_His').'.pdf');
    }

    private function authorizeAuditLogsExport(Request $request): void
    {
        abort_unless($request->user()?->canViewAuditLogs(), 403);
        if (tenant_id() === null) {
            abort(403);
        }
    }

    private function authorizeExport(Request $request): void
    {
        abort_unless($request->user()?->canExportTenantReports(), 403);
    }

    private function tenantOrganizationId(): int
    {
        $id = tenant_id();
        if ($id === null) {
            abort(403);
        }

        return $id;
    }
}
