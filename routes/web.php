<?php

use App\Http\Controllers\AccountsReceivableController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CourierShipmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DataExportController;
use App\Http\Controllers\FinancialReportsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizationSwitcherController;
use App\Http\Controllers\OrganizationUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GeoController;
use App\Http\Controllers\PublicTrackingController;
use App\Http\Controllers\ServiceRateController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ShipmentEvidenceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/seguimiento', [PublicTrackingController::class, 'search'])
    ->name('tracking.search');

Route::post('/seguimiento/consultar', [PublicTrackingController::class, 'lookup'])
    ->name('tracking.lookup');

Route::get('/seguimiento/{organization_slug}/{tracking_number}', [PublicTrackingController::class, 'show'])
    ->name('tracking.public');

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'tenant'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/organization/switch', [OrganizationSwitcherController::class, 'update'])
        ->name('organization.switch');
});

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/logs', [ActivityLogController::class, 'index'])
        ->name('logs.index');

    Route::prefix('exports')->group(function () {
        Route::get('/shipments/excel', [DataExportController::class, 'shipmentsExcel'])
            ->name('exports.shipments.excel');
        Route::get('/shipments/pdf', [DataExportController::class, 'shipmentsPdf'])
            ->name('exports.shipments.pdf');
        Route::get('/customers/excel', [DataExportController::class, 'customersExcel'])
            ->name('exports.customers.excel');
        Route::get('/customers/pdf', [DataExportController::class, 'customersPdf'])
            ->name('exports.customers.pdf');
        Route::get('/users/excel', [DataExportController::class, 'usersExcel'])
            ->name('exports.users.excel');
        Route::get('/users/pdf', [DataExportController::class, 'usersPdf'])
            ->name('exports.users.pdf');
        Route::get('/logs/excel', [DataExportController::class, 'logsExcel'])
            ->name('exports.logs.excel');
        Route::get('/logs/pdf', [DataExportController::class, 'logsPdf'])
            ->name('exports.logs.pdf');
    });

    Route::get('/geo/cities', [GeoController::class, 'citiesByDepartment'])
        ->name('geo.cities');

    Route::get('/customers/search', [CustomerController::class, 'search'])
        ->name('customers.search');

    Route::get('/customers/{customer}/addresses', [CustomerController::class, 'addressesJson'])
        ->name('customers.addresses');

    Route::patch('/customers/{customer}/deactivate', [CustomerController::class, 'deactivate'])
        ->name('customers.deactivate');

    Route::delete('/customers/{customer}/force', [CustomerController::class, 'forceDestroy'])
        ->name('customers.force-destroy');

    Route::resource('customers', CustomerController::class);

    Route::get('/users', [OrganizationUserController::class, 'index'])
        ->name('users.index');

    Route::get('/users/create', [OrganizationUserController::class, 'create'])
        ->name('users.create');

    Route::post('/users', [OrganizationUserController::class, 'store'])
        ->name('users.store');

    Route::patch('/users/{user}', [OrganizationUserController::class, 'update'])
        ->name('users.update');

    Route::delete('/users/{user}', [OrganizationUserController::class, 'destroy'])
        ->name('users.destroy');

    Route::get('/my-shipments', [CourierShipmentController::class, 'index'])
        ->name('courier.shipments.index');

    Route::post('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])
        ->name('shipments.status.update');

    Route::post('/shipments/{shipment}/evidences', [ShipmentEvidenceController::class, 'store'])
        ->name('shipments.evidences.store');

    Route::patch('/shipments/{shipment}/deactivate', [ShipmentController::class, 'deactivate'])
        ->name('shipments.deactivate');

    Route::patch('/shipments/{shipment}/payment', [ShipmentController::class, 'updatePayment'])
        ->name('shipments.payment.update');

    Route::get('/financial-reports', FinancialReportsController::class)
        ->name('financial.reports');

    Route::get('/financial/receivables', AccountsReceivableController::class)
        ->name('financial.receivables');

    Route::resource('service-rates', ServiceRateController::class)->except(['show']);

    Route::resource('shipments', ShipmentController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);

    Route::get('/shipments/{shipment}/guide', [ShipmentController::class, 'guide'])
        ->name('shipments.guide');

    Route::get('/shipments/{shipment}/guide/pdf', [ShipmentController::class, 'guidePdf'])
        ->name('shipments.guide.pdf');

    Route::get('/shipments/{shipment}/report/pdf', [ShipmentController::class, 'reportPdf'])
        ->name('shipments.report.pdf');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
