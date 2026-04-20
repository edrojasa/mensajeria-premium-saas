<?php

namespace App\Http\Controllers;

use App\Finance\PaymentStatus;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountsReceivableController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()?->canAccessFinancialModule(), 403);

        $shipments = Shipment::query()
            ->with(['customer:id,name,customer_code'])
            ->where('payment_status', PaymentStatus::PENDING)
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('financial.receivables', compact('shipments'));
    }
}
