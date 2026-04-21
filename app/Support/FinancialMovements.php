<?php

namespace App\Support;

use App\Finance\PaymentStatus;
use App\Models\Shipment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class FinancialMovements
{
    public static function filteredShipmentsQuery(Request $request, ?int $customerId = null): Builder
    {
        $tenantId = tenant_id();
        if ($tenantId === null) {
            abort(403);
        }

        $from = $request->query('from');
        $to = $request->query('to');
        $status = $request->query('status');
        $customer = $request->query('customer_id');
        $user = $request->query('user_id');
        $type = $request->query('type');
        $search = trim((string) $request->query('q', ''));

        $query = Shipment::query()
            ->with(['customer:id,name,customer_code', 'creator:id,name'])
            ->where('organization_id', $tenantId)
            ->select('shipments.*')
            ->selectRaw('COALESCE(payment_date, created_at) as movement_at');

        if ($customerId !== null) {
            $query->where('customer_id', $customerId);
        }

        $query->when($from, fn (Builder $q) => $q->whereDate(DB::raw('COALESCE(payment_date, created_at)'), '>=', $from))
            ->when($to, fn (Builder $q) => $q->whereDate(DB::raw('COALESCE(payment_date, created_at)'), '<=', $to))
            ->when($status, fn (Builder $q) => $q->where('payment_status', $status))
            ->when($customer, fn (Builder $q) => $q->where('customer_id', (int) $customer))
            ->when($user, fn (Builder $q) => $q->where('created_by_user_id', (int) $user))
            ->when($type === 'pago', fn (Builder $q) => $q->where('payment_status', PaymentStatus::PAID))
            ->when($type === 'cargo', fn (Builder $q) => $q->where('payment_status', PaymentStatus::PENDING))
            ->when($search !== '', function (Builder $q) use ($search): void {
                $q->where(function (Builder $inner) use ($search): void {
                    $inner->where('tracking_number', 'like', '%'.$search.'%')
                        ->orWhereHas('customer', function (Builder $cq) use ($search): void {
                            $cq->where('name', 'like', '%'.$search.'%')
                                ->orWhere('customer_code', 'like', '%'.$search.'%');
                        });
                });
            })
            ->orderByDesc('movement_at')
            ->orderByDesc('id');

        return $query;
    }

    public static function paginated(Request $request, ?int $customerId = null, string $pageName = 'page'): LengthAwarePaginator
    {
        $paginator = self::filteredShipmentsQuery($request, $customerId)
            ->paginate(20, ['*'], $pageName)
            ->withQueryString();

        $paginator->setCollection(self::mapShipmentsToMovements($paginator->getCollection()));

        return $paginator;
    }

    /**
     * @param  Collection<int, Shipment>  $shipments
     * @return Collection<int, array<string, mixed>>
     */
    public static function mapShipmentsToMovements(Collection $shipments): Collection
    {
        return $shipments->map(function (Shipment $shipment): array {
            $isPaid = $shipment->payment_status === PaymentStatus::PAID;
            $value = $isPaid
                ? (float) ($shipment->paid_amount ?? $shipment->cost ?? 0)
                : (float) $shipment->balanceDue();

            return [
                'date' => $shipment->payment_date ?? $shipment->created_at,
                'customer' => $shipment->customer?->name ?? __('finance.unknown_customer_group'),
                'tracking' => $shipment->tracking_number,
                'value' => $value,
                'status' => $shipment->payment_status ?? PaymentStatus::PENDING,
                'user' => $shipment->creator?->name ?? '—',
                'type' => $isPaid ? 'pago' : 'cargo',
                'shipment' => $shipment,
            ];
        });
    }
}
