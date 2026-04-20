<?php

namespace App\Http\Controllers;

use App\Audit\AuditActions;
use App\Finance\PaymentStatus;
use App\Models\City;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Shipment;
use App\Services\ActivityLogger;
use App\Support\CustomersListing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Customer::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:64'],
            'phone' => ['required', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'addresses' => ['nullable', 'array'],
            'addresses.*.label' => ['required_with:addresses', 'string', 'max:64'],
            'addresses.*.address_line' => ['required_with:addresses', 'string', 'max:500'],
            'addresses.*.department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'addresses.*.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'addresses.*.reference_notes' => ['nullable', 'string', 'max:500'],
            'addresses.*.is_default' => ['nullable', 'boolean'],
        ]);

        $customer = new Customer([
            'name' => $validated['name'],
            'document' => $validated['document'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);
        $customer->save();

        $this->syncAddressesFromRequest($customer, $validated['addresses'] ?? []);

        ActivityLogger::log(
            $request->user(),
            AuditActions::CUSTOMER_CREATED,
            __('audit.customer_created', ['name' => $customer->name]),
            $customer
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
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $financialBilled = Shipment::query()
            ->where('customer_id', $customer->id)
            ->sum('cost');

        $financialPaid = Shipment::query()
            ->where('customer_id', $customer->id)
            ->where('payment_status', PaymentStatus::PAID)
            ->sum('paid_amount');

        $financialBalance = Shipment::query()
            ->where('customer_id', $customer->id)
            ->get()
            ->sum(fn (Shipment $s) => $s->balanceDue());

        return view('customers.show', compact(
            'customer',
            'shipments',
            'financialBilled',
            'financialPaid',
            'financialBalance',
            'activeTab',
            'canFinance'
        ));
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

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $this->authorize('update', $customer);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:64'],
            'phone' => ['required', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'addresses' => ['nullable', 'array'],
            'addresses.*.label' => ['required_with:addresses', 'string', 'max:64'],
            'addresses.*.address_line' => ['required_with:addresses', 'string', 'max:500'],
            'addresses.*.department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'addresses.*.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'addresses.*.reference_notes' => ['nullable', 'string', 'max:500'],
            'addresses.*.is_default' => ['nullable', 'boolean'],
        ]);

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
            $customer
        );

        return redirect()
            ->route('customers.show', $customer)
            ->with('status', __('customers.updated'));
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
