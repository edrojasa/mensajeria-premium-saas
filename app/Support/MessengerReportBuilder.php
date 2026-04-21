<?php

namespace App\Support;

use App\Finance\PaymentStatus;
use App\Models\Shipment;
use App\Models\User;
use App\Organizations\OrganizationRole;
use App\Shipments\ShipmentStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class MessengerReportBuilder
{
    public static function filteredShipmentsQuery(Request $request): Builder
    {
        $orgId = tenant_id();
        if ($orgId === null) {
            abort(403);
        }

        $from = $request->query('from');
        $to = $request->query('to');
        $status = $request->query('status');
        $customer = $request->query('customer_id');
        $paymentStatus = $request->query('payment_status');
        $messenger = $request->query('messenger_id');
        $search = trim((string) $request->query('q', ''));

        return Shipment::query()
            ->with(['customer:id,name,customer_code', 'assignedCourier:id,name'])
            ->where('organization_id', $orgId)
            ->whereNotNull('assigned_user_id')
            ->when($from, fn (Builder $q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn (Builder $q) => $q->whereDate('created_at', '<=', $to))
            ->when($status, fn (Builder $q) => $q->where('status', $status))
            ->when($customer, fn (Builder $q) => $q->where('customer_id', (int) $customer))
            ->when($paymentStatus, fn (Builder $q) => $q->where('payment_status', $paymentStatus))
            ->when($messenger, fn (Builder $q) => $q->where('assigned_user_id', (int) $messenger))
            ->when($search !== '', function (Builder $q) use ($search): void {
                $q->where(function (Builder $inner) use ($search): void {
                    $inner->where('tracking_number', 'like', '%'.$search.'%')
                        ->orWhereHas('customer', fn (Builder $c) => $c->where('name', 'like', '%'.$search.'%'))
                        ->orWhereHas('assignedCourier', fn (Builder $u) => $u->where('name', 'like', '%'.$search.'%'));
                });
            });
    }

    public static function metrics(Request $request): array
    {
        $base = self::filteredShipmentsQuery($request);

        return [
            'total_income' => (float) (clone $base)->where('status', ShipmentStatus::DELIVERED)->sum('cost'),
            'total_pending_collection' => (float) (clone $base)->where('payment_status', PaymentStatus::PENDING)->sum('cost'),
            'total_shipments' => (int) (clone $base)->count(),
        ];
    }

    public static function messengersSummary(Request $request): Collection
    {
        $orgId = tenant_id();
        if ($orgId === null) {
            abort(403);
        }

        $agg = self::filteredShipmentsQuery($request)
            ->selectRaw('assigned_user_id')
            ->selectRaw('COUNT(*) as total_shipments')
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as delivered", [ShipmentStatus::DELIVERED])
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending", [ShipmentStatus::RECEIVED])
            ->selectRaw("SUM(CASE WHEN status = ? THEN cost ELSE 0 END) as income_generated", [ShipmentStatus::DELIVERED])
            ->selectRaw("SUM(CASE WHEN payment_status = ? THEN cost ELSE 0 END) as pending_collection", [PaymentStatus::PENDING])
            ->groupBy('assigned_user_id')
            ->get()
            ->keyBy('assigned_user_id');

        $messengers = User::query()
            ->join('organization_user', 'users.id', '=', 'organization_user.user_id')
            ->where('organization_user.organization_id', $orgId)
            ->where('organization_user.role', OrganizationRole::MENSAJERO)
            ->select('users.id', 'users.name', 'organization_user.is_active')
            ->orderBy('users.name')
            ->get();

        return $messengers->map(function ($messenger) use ($agg): array {
            $row = $agg->get($messenger->id);

            return [
                'id' => (int) $messenger->id,
                'name' => $messenger->name,
                'active' => (bool) $messenger->is_active,
                'total_shipments' => (int) ($row->total_shipments ?? 0),
                'delivered' => (int) ($row->delivered ?? 0),
                'pending' => (int) ($row->pending ?? 0),
                'income_generated' => (float) ($row->income_generated ?? 0),
                'pending_collection' => (float) ($row->pending_collection ?? 0),
            ];
        });
    }

    public static function detailMetrics(Request $request, int $messengerId): array
    {
        $base = self::filteredShipmentsQuery($request)->where('assigned_user_id', $messengerId);

        return [
            'total' => (int) (clone $base)->count(),
            'delivered' => (int) (clone $base)->where('status', ShipmentStatus::DELIVERED)->count(),
            'in_route' => (int) (clone $base)->whereIn('status', [ShipmentStatus::IN_TRANSIT, ShipmentStatus::OUT_FOR_DELIVERY])->count(),
            'pending' => (int) (clone $base)->where('status', ShipmentStatus::RECEIVED)->count(),
            'cancelled' => (int) (clone $base)->where('status', ShipmentStatus::CANCELLED)->count(),
            'income_generated' => (float) (clone $base)->where('status', ShipmentStatus::DELIVERED)->sum('cost'),
            'pending_collection' => (float) (clone $base)->where('payment_status', PaymentStatus::PENDING)->sum('cost'),
        ];
    }

    public static function history(Request $request): LengthAwarePaginator
    {
        return self::filteredShipmentsQuery($request)
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();
    }

    public static function statusChart(Request $request, int $messengerId): array
    {
        $rows = self::filteredShipmentsQuery($request)
            ->where('assigned_user_id', $messengerId)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        return [
            'labels' => $rows->pluck('status')->map(fn ($s) => \App\Shipments\ShipmentStatus::label($s))->values(),
            'data' => $rows->pluck('total')->values(),
        ];
    }

    public static function incomeByDateChart(Request $request, int $messengerId): array
    {
        $rows = self::filteredShipmentsQuery($request)
            ->where('assigned_user_id', $messengerId)
            ->where('status', ShipmentStatus::DELIVERED)
            ->selectRaw('DATE(created_at) as dt')
            ->selectRaw('SUM(cost) as total')
            ->groupBy('dt')
            ->orderBy('dt')
            ->get();

        return [
            'labels' => $rows->pluck('dt')->values(),
            'data' => $rows->pluck('total')->map(fn ($v) => (float) $v)->values(),
        ];
    }

    public static function deliveredEvolutionChart(Request $request, int $messengerId): array
    {
        $rows = self::filteredShipmentsQuery($request)
            ->where('assigned_user_id', $messengerId)
            ->where('status', ShipmentStatus::DELIVERED)
            ->selectRaw('DATE(created_at) as dt')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('dt')
            ->orderBy('dt')
            ->get();

        return [
            'labels' => $rows->pluck('dt')->values(),
            'data' => $rows->pluck('total')->map(fn ($v) => (int) $v)->values(),
        ];
    }
}
