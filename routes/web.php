<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizationSwitcherController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GeoController;
use App\Http\Controllers\PublicTrackingController;
use App\Http\Controllers\ShipmentController;
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
    Route::get('/geo/cities', [GeoController::class, 'citiesByDepartment'])
        ->name('geo.cities');

    Route::post('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])
        ->name('shipments.status.update');

    Route::resource('shipments', ShipmentController::class)->only(['index', 'create', 'store', 'show']);

    Route::get('/shipments/{shipment}/guide', [ShipmentController::class, 'guide'])
        ->name('shipments.guide');

    Route::get('/shipments/{shipment}/guide/pdf', [ShipmentController::class, 'guidePdf'])
        ->name('shipments.guide.pdf');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
