<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use App\Exports\OrganizationUsersExport;
use App\Exports\ShipmentsExport;
use App\Support\CustomersListing;
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

        return Excel::download(
            new CustomersExport($query),
            'clientes-'.now()->format('Y-m-d_His').'.xlsx'
        );
    }

    public function customersPdf(Request $request): Response
    {
        $this->authorizeExport($request);

        $rows = CustomersListing::filteredQuery($request)->get();

        $pdf = Pdf::loadView('exports.pdf.customers', [
            'rows' => $rows,
            'title' => __('exports.pdf_customers_title'),
            'generatedAt' => now()->timezone(config('app.timezone')),
        ])->setPaper('a4', 'portrait');

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

    public function messengersExcel(Request $request): BinaryFileResponse
    {
        $this->authorizeExport($request);

        $orgId = $this->tenantOrganizationId();
        $query = OrganizationUsersListing::messengersQuery($request, $orgId);

        return Excel::download(
            new OrganizationUsersExport($query, $orgId),
            'mensajeros-'.now()->format('Y-m-d_His').'.xlsx'
        );
    }

    public function messengersPdf(Request $request): Response
    {
        $this->authorizeExport($request);

        $orgId = $this->tenantOrganizationId();
        $rows = OrganizationUsersListing::messengersQuery($request, $orgId)->get();

        $pdf = Pdf::loadView('exports.pdf.users', [
            'rows' => $rows,
            'organizationId' => $orgId,
            'title' => __('exports.pdf_messengers_title'),
            'generatedAt' => now()->timezone(config('app.timezone')),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('mensajeros-'.now()->format('Y-m-d_His').'.pdf');
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
