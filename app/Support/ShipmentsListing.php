<?php

namespace App\Support;

use App\Models\Shipment;
use App\Shipments\ShipmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class ShipmentsListing
{
    /**
     * Listado / exportaciones de envíos con los mismos filtros GET.
     */
    public static function filteredQuery(Request $request): Builder
    {
        $deliveredSub = DB::table('shipment_status_histories')
            ->select('shipment_id', DB::raw('MAX(created_at) as delivered_logged_at'))
            ->where('to_status', ShipmentStatus::DELIVERED)
            ->when(tenant_id() !== null, fn ($q) => $q->where('organization_id', tenant_id()))
            ->groupBy('shipment_id');

        $query = Shipment::query()
            ->with(['customer:id,name', 'assignedCourier:id,name'])
            ->leftJoinSub($deliveredSub, 'delivered_hist', function ($join): void {
                $join->on('shipments.id', '=', 'delivered_hist.shipment_id');
            })
            ->addSelect([
                'shipments.*',
                'delivered_hist.delivered_logged_at',
            ]);

        $query->when($request->filled('date_from'), function (Builder $q) use ($request): void {
            $q->whereDate('shipments.created_at', '>=', $request->query('date_from'));
        });

        $query->when($request->filled('date_to'), function (Builder $q) use ($request): void {
            $q->whereDate('shipments.created_at', '<=', $request->query('date_to'));
        });

        $query->when($request->filled('status'), function (Builder $q) use ($request): void {
            $q->where('shipments.status', $request->query('status'));
        });

        $query->when($request->filled('customer_id'), function (Builder $q) use ($request): void {
            $q->where('shipments.customer_id', (int) $request->query('customer_id'));
        });

        $query->when($request->filled('assigned_user_id'), function (Builder $q) use ($request): void {
            $q->where('shipments.assigned_user_id', (int) $request->query('assigned_user_id'));
        });

        return $query->orderByDesc('shipments.created_at');
    }
}
