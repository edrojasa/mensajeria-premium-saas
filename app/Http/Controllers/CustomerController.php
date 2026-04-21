<?php

namespace App\Http\Controllers;

use App\Audit\AuditActions;
use App\Exports\FinancialMovementsExport;
use App\Finance\PaymentStatus;
use App\Models\City;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Shipment;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Services\ActivityLogger;
use App\Support\CustomersListing;
use App\Support\FinancialMovements;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Customer::class);

        $customers = CustomersListing::filteredQuery($request)->paginate(15)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function search(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('viewAny', Customer::class);

        $q = trim((string) $request->query('q', ''));

        $customers = Customer::query()
            ->when(! $request->boolean('include_inactive'), fn ($qb) => $qb->where('customers.is_active', true))
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', '%'.$q.'%')
                        ->orWhere('phone', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('customer_code', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('name')
            ->limit(30)
            ->get(['id', 'customer_code', 'name', 'phone', 'email']);

        return response()->json(['data' => $customers]);
    }

    public function addressesJson(Customer $customer): \Illuminate\Http\JsonResponse
    {
        $this->authorize('view', $customer);

        $addresses = $customer->addresses()->orderByDesc('is_default')->orderBy('label')->get([
            'id',
            'label',
            'address_line',
            'department_id',
            'city_id',
            'city',
            'department',
            'reference_notes',
            'is_default',
        ]);

        return response()->json(['data' => $addresses]);
    }

    public function create(): View
    {
        $this->authorize('create', Customer::class);

        $departments = Department::query()
            ->where('country_code', 'CO')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('customers.create', compact('departments'));
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $customer = new Customer([
            'name' => $validated['name'],
            'document' => $validated['document'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
        ]);
        $customer->save();

        $this->syncAddressesFromRequest($customer, $validated['addresses'] ?? []);

        ActivityLogger::log(
            $request->user(),
            AuditActions::CUSTOMER_CREATED,
            __('audit.customer_created', ['name' => $customer->name]),
            $customer,
            [
                'model' => Customer::class,
                'record_id' => $customer->id,
                'changes' => [
                    'before' => [],
                    'after' => $customer->only(['name', 'document', 'phone', 'email', 'is_active']),
                ],
            ]
        );

        return redirect()
            ->route('customers.show', $customer)
            ->with('status', __('customers.saved'));
    }

    public function show(Request $request, Customer $customer): View
    {
        $this->authorize('view', $customer);

        $customer->load([
            'addresses' => fn ($q) => $q->orderByDesc('is_default')->orderBy('label'),
        ]);

        $canFinance = $request->user()->canAccessFinancialModule();
        $activeTab = ($canFinance && $request->query('tab') === 'financial') ? 'financial' : 'shipments';

        $shipments = Shipment::query()
            ->where('organization_id', $customer->organization_id)
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate(10, ['*'], 'shipments_page')
            ->withQueryString();

        $financialBilled = Shipment::query()
            ->where('organization_id', $customer->organization_id)
            ->where('customer_id', $customer->id)
            ->sum('cost');

        $financialPaid = Shipment::query()
            ->where('organization_id', $customer->organization_id)
            ->where('customer_id', $customer->id)
            ->where('payment_status', PaymentStatus::PAID)
            ->sum('paid_amount');

        $financialBalance = Shipment::query()
            ->where('organization_id', $customer->organization_id)
            ->where('customer_id', $customer->id)
            ->get()
            ->sum(fn (Shipment $s) => $s->balanceDue());

        $financialMovements = $canFinance
            ? FinancialMovements::paginated($request, (int) $customer->id, 'financial_page')
            : null;

        return view('customers.show', compact(
            'customer',
            'shipments',
            'financialBilled',
            'financialPaid',
            'financialBalance',
            'activeTab',
            'canFinance',
            'financialMovements'
        ));
    }

    public function financialPdf(Request $request, Customer $customer): Response
    {
        $this->authorize('view', $customer);
        abort_unless($request->user()?->canAccessFinancialModule(), 403);

        $rows = FinancialMovements::mapShipmentsToMovements(
            FinancialMovements::filteredShipmentsQuery($request, (int) $customer->id)->get()
        );

        $pdf = Pdf::loadView('customers.pdf.financial-movements', [
            'title' => __('finance.customer_finance_title').' - '.$customer->name,
            'generatedAt' => now(),
            'customer' => $customer,
            'rows' => $rows,
            'total' => (float) $rows->sum('value'),
            'paidCount' => (int) $rows->where('status', PaymentStatus::PAID)->count(),
            'pendingCount' => (int) $rows->where('status', PaymentStatus::PENDING)->count(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('cliente-finanzas-'.$customer->id.'-'.now()->format('Y-m-d_His').'.pdf');
    }

    public function financialExcel(Request $request, Customer $customer): BinaryFileResponse
    {
        $this->authorize('view', $customer);
        abort_unless($request->user()?->canAccessFinancialModule(), 403);

        $rows = FinancialMovements::mapShipmentsToMovements(
            FinancialMovements::filteredShipmentsQuery($request, (int) $customer->id)->get()
        );

        return Excel::download(
            new FinancialMovementsExport($rows),
            'cliente-finanzas-'.$customer->id.'-'.now()->format('Y-m-d_His').'.xlsx'
        );
    }

    public function edit(Customer $customer): View
    {
        $this->authorize('update', $customer);

        $departments = Department::query()
            ->where('country_code', 'CO')
            ->orderBy('name')
            ->get(['id', 'name']);

        $customer->load(['addresses']);

        return view('customers.edit', compact('customer', 'departments'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validated();

        $before = $customer->only(['name', 'document', 'phone', 'email', 'is_active']);
        $customer->update([
            'name' => $validated['name'],
            'document' => $validated['document'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $customer->addresses()->delete();
        $this->syncAddressesFromRequest($customer, $validated['addresses'] ?? []);

        ActivityLogger::log(
            $request->user(),
            AuditActions::CUSTOMER_UPDATED,
            __('audit.customer_updated', ['name' => $customer->name]),
            $customer,
            [
                'model' => Customer::class,
                'record_id' => $customer->id,
                'changes' => [
                    'before' => $before,
                    'after' => $customer->only(['name', 'document', 'phone', 'email', 'is_active']),
                ],
            ]
        );

        return redirect()
            ->route('customers.show', $customer)
            ->with('status', __('customers.updated'));
    }

    public function deactivate(Request $request, Customer $customer): RedirectResponse
    {
        $this->authorize('deactivate', $customer);

        if (! $customer->is_active) {
            return redirect()
                ->route('customers.index')
                ->with('status', __('customers.deactivated_success'));
        }

        $updated = Customer::query()
            ->whereKey($customer->id)
            ->where('organization_id', $customer->organization_id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        if ($updated !== 1) {
            return redirect()
                ->route('customers.index')
                ->withErrors(__('customers.deactivate_failed'));
        }

        ActivityLogger::log(
            $request->user(),
            AuditActions::CUSTOMER_DEACTIVATED,
            __('audit.customer_deactivated', ['name' => $customer->name]),
            $customer,
            [
                'model' => Customer::class,
                'record_id' => $customer->id,
                'changes' => [
                    'before' => ['is_active' => true],
                    'after' => ['is_active' => false],
                ],
            ]
        );

        return redirect()
            ->route('customers.index')
            ->with('status', __('customers.deactivated_success'));
    }

    public function activate(Request $request, Customer $customer): RedirectResponse
    {
        $this->authorize('deactivate', $customer);

        if ($customer->is_active) {
            return redirect()
                ->route('customers.index')
                ->with('status', __('customers.activated_success'));
        }

        $updated = Customer::query()
            ->whereKey($customer->id)
            ->where('organization_id', $customer->organization_id)
            ->where('is_active', false)
            ->update(['is_active' => true]);

        if ($updated !== 1) {
            return redirect()
                ->route('customers.index')
                ->withErrors(__('customers.activate_failed'));
        }

        ActivityLogger::log(
            $request->user(),
            AuditActions::CUSTOMER_UPDATED,
            __('audit.customer_activated', ['name' => $customer->name]),
            $customer,
            [
                'model' => Customer::class,
                'record_id' => $customer->id,
                'changes' => [
                    'before' => ['is_active' => false],
                    'after' => ['is_active' => true],
                ],
            ]
        );

        return redirect()
            ->route('customers.index')
            ->with('status', __('customers.activated_success'));
    }

    public function forceDestroy(Request $request, Customer $customer): RedirectResponse
    {
        $this->authorize('forceDestroy', $customer);

        if ($customer->shipments()->exists()) {
            return redirect()
                ->route('customers.show', $customer)
                ->withErrors(__('customers.force_delete_blocked_shipments'));
        }

        DB::transaction(function () use ($customer, $request): void {
            $customer->addresses()->delete();

            ActivityLogger::log(
                $request->user(),
                AuditActions::CUSTOMER_FORCE_DELETED,
                __('audit.customer_force_deleted', ['name' => $customer->name]),
                null,
                [
                    'model' => Customer::class,
                    'record_id' => $customer->id,
                    'action' => 'delete_force',
                ]
            );

            $customer->delete();
        });

        return redirect()
            ->route('customers.index')
            ->with('status', __('customers.force_deleted_success'));
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function syncAddressesFromRequest(Customer $customer, array $rows): void
    {
        $hasDefault = false;

        foreach ($rows as $row) {
            if (empty($row['label']) || empty($row['address_line'])) {
                continue;
            }

            $cityId = $row['city_id'] ?? null;
            $departmentId = $row['department_id'] ?? null;
            $cityName = '';
            $departmentName = null;

            if ($cityId) {
                $city = City::query()->with('department')->find($cityId);
                if ($city) {
                    $cityName = $city->name;
                    $departmentName = $city->department->name;
                    $departmentId = $city->department_id;
                }
            }

            $isDefault = ! empty($row['is_default']);
            if ($isDefault) {
                $hasDefault = true;
            }

            $customer->addresses()->create([
                'label' => $row['label'],
                'address_line' => $row['address_line'],
                'city' => $cityName !== '' ? $cityName : ($row['city'] ?? ''),
                'department' => $departmentName,
                'department_id' => $departmentId,
                'city_id' => $cityId,
                'reference_notes' => $row['reference_notes'] ?? null,
                'is_default' => $isDefault,
            ]);
        }

        if ($hasDefault) {
            $firstId = $customer->addresses()->where('is_default', true)->orderBy('id')->value('id');
            if ($firstId) {
                $customer->addresses()->where('id', '!=', $firstId)->update(['is_default' => false]);
            }
        }
    }
}
