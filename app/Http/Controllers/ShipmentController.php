<?php

namespace App\Http\Controllers;

use App\Audit\AuditActions;
use App\Finance\PaymentStatus;
use App\Finance\PaymentType;
use App\Finance\ServiceType;
use App\Finance\ShipmentCostCalculator;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentPaymentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Http\Requests\UpdateShipmentStatusRequest;
use App\Http\Middleware\RedirectCourierFromStaffShipmentRoutes;
use App\Models\City;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Department;
use App\Models\ServiceRate;
use App\Models\Shipment;
use App\Models\User;
use App\Exports\ShipmentHistoryExport;
use App\Organizations\OrganizationRole;
use App\Shipments\ShipmentStatus;
use App\Services\ActivityLogger;
use App\Shipments\ShipmentTransitionRules;
use App\Shipments\ShipmentTransitionService;
use App\Shipments\TrackingNumberGenerator;
use App\Support\ShipmentsListing;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ShipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(RedirectCourierFromStaffShipmentRoutes::class)->only(['index', 'create', 'edit']);
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Shipment::class);

        $shipments = ShipmentsListing::filteredQuery($request)->paginate(15)->withQueryString();

        $customers = Customer::query()->active()->orderBy('name')->get(['id', 'name']);
        $messengers = $this->messengersForTenant();

        $statuses = collect(ShipmentStatus::all())
            ->mapWithKeys(fn (string $key) => [$key => ShipmentStatus::label($key)])
            ->all();

        return view('shipments.index', compact('shipments', 'customers', 'messengers', 'statuses'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Shipment::class);

        $departments = Department::query()
            ->where('country_code', 'CO')
            ->orderBy('name')
            ->get(['id', 'name']);

        $messengers = $this->messengersForTenant();

        $initialCustomers = Customer::query()
            ->active()
            ->orderBy('name')
            ->limit(40)
            ->get(['id', 'customer_code', 'name', 'phone', 'email']);

        $preCustomerId = $request->query('customer_id');
        if ($preCustomerId && ! $initialCustomers->contains('id', (int) $preCustomerId)) {
            $extra = Customer::query()->find((int) $preCustomerId);
            if ($extra) {
                $initialCustomers = $initialCustomers->prepend($extra);
            }
        }

        return view('shipments.create', compact('departments', 'messengers', 'initialCustomers', 'preCustomerId'));
    }

    public function store(
        StoreShipmentRequest $request,
        ShipmentTransitionService $transitionService,
        TrackingNumberGenerator $trackingNumberGenerator
    ): RedirectResponse {
        $this->authorize('create', Shipment::class);

        $tenantId = tenant_id();

        if ($tenantId === null) {
            abort(403);
        }

        $validated = $request->validated();

        return DB::transaction(function () use ($validated, $transitionService, $trackingNumberGenerator, $request, $tenantId) {
            if ($validated['customer_mode'] === 'new') {
                $customer = Customer::create([
                    'name' => $validated['new_customer_name'],
                    'document' => $validated['new_customer_document'] ?? null,
                    'phone' => $validated['new_customer_phone'],
                    'email' => $validated['new_customer_email'] ?? null,
                    'notes' => $validated['new_customer_notes'] ?? null,
                ]);
                $validated['customer_id'] = $customer->id;
            } elseif ($validated['customer_mode'] === 'skip') {
                $validated['customer_id'] = null;
            }

            $attrs = $this->attributesFromShipmentForm($validated);
            $rate = $this->resolveShipmentRate($tenantId, $attrs['service_type'] ?? ServiceType::STANDARD);
            $attrs['service_rate_id'] = $rate?->id;
            $attrs['cost'] = app(ShipmentCostCalculator::class)->calculate(
                $rate,
                $attrs['weight_kg'] ?? null,
                $attrs['distance_km'] ?? null
            );
            $attrs['payment_status'] = PaymentStatus::PENDING;

            $shipment = new Shipment($attrs);
            $shipment->tracking_number = $trackingNumberGenerator->generate($tenantId);
            $shipment->status = ShipmentStatus::RECEIVED;
            $shipment->created_by_user_id = $request->user()->id;

            $transitionService->createShipmentWithInitialStatus($shipment, $request->user());

            ActivityLogger::log(
                $request->user(),
                AuditActions::SHIPMENT_CREATED,
                __('audit.shipment_created', ['tracking' => $shipment->tracking_number]),
                $shipment,
                $this->auditMetaPayload(
                    ['customer_id', 'service_type', 'service_rate_id', 'cost', 'payment_type', 'payment_status'],
                    [],
                    $shipment->only(['customer_id', 'service_type', 'service_rate_id', 'cost', 'payment_type', 'payment_status']),
                    (int) $shipment->id
                )
            );

            return redirect()
                ->route('shipments.show', $shipment)
                ->with('status', __('shipments.created_success'));
        });
    }

    public function edit(Shipment $shipment): View
    {
        $this->authorize('update', $shipment);

        $departments = Department::query()
            ->where('country_code', 'CO')
            ->orderBy('name')
            ->get(['id', 'name']);

        $messengers = $this->messengersForTenant();

        $shipment->load(['customer.addresses']);

        $initialCustomers = Customer::query()
            ->active()
            ->orderBy('name')
            ->limit(40)
            ->get(['id', 'customer_code', 'name', 'phone', 'email']);

        if ($shipment->customer_id && ! $initialCustomers->contains('id', $shipment->customer_id)) {
            $extra = Customer::query()->find($shipment->customer_id);
            if ($extra) {
                $initialCustomers = $initialCustomers->prepend($extra);
            }
        }

        return view('shipments.edit', compact('shipment', 'departments', 'messengers', 'initialCustomers'));
    }

    public function update(
        UpdateShipmentRequest $request,
        Shipment $shipment,
    ): RedirectResponse {
        $this->authorize('update', $shipment);

        $validated = $request->validated();
        $before = $shipment->only([
            'customer_id',
            'assigned_user_id',
            'service_type',
            'service_rate_id',
            'cost',
            'payment_type',
            'status',
        ]);

        $beforeAssign = $shipment->assigned_user_id;

        DB::transaction(function () use ($validated, $shipment) {
            if ($validated['customer_mode'] === 'new') {
                $customer = Customer::create([
                    'name' => $validated['new_customer_name'],
                    'document' => $validated['new_customer_document'] ?? null,
                    'phone' => $validated['new_customer_phone'],
                    'email' => $validated['new_customer_email'] ?? null,
                    'notes' => $validated['new_customer_notes'] ?? null,
                ]);
                $validated['customer_id'] = $customer->id;
            } elseif ($validated['customer_mode'] === 'skip') {
                $validated['customer_id'] = null;
            }

            $attrs = $this->attributesFromShipmentForm($validated);
            $rate = $this->resolveShipmentRate((int) $shipment->organization_id, $attrs['service_type'] ?? ServiceType::STANDARD);
            $attrs['service_rate_id'] = $rate?->id;
            $attrs['cost'] = app(ShipmentCostCalculator::class)->calculate(
                $rate,
                $attrs['weight_kg'] ?? null,
                $attrs['distance_km'] ?? null
            );

            $shipment->fill($attrs);
            $shipment->save();
        });

        $shipment->refresh();
        $shipment->load('assignedCourier');

        if ((int) $beforeAssign !== (int) ($shipment->assigned_user_id ?? 0)) {
            $name = $shipment->assignedCourier?->name ?? __('shipments.unassigned_courier');
            ActivityLogger::log(
                $request->user(),
                AuditActions::SHIPMENT_MESSENGER_ASSIGNED,
                __('audit.shipment_messenger_assigned', ['name' => $name, 'tracking' => $shipment->tracking_number]),
                $shipment
            );
        }

        ActivityLogger::log(
            $request->user(),
            AuditActions::SHIPMENT_UPDATED,
            __('audit.shipment_updated', ['tracking' => $shipment->tracking_number]),
            $shipment,
            $this->auditMetaPayload(array_keys($before), $before, $shipment->only(array_keys($before)), (int) $shipment->id)
        );

        $this->logShipmentSpecificChanges($request, $shipment, $before);

        return redirect()
            ->route('shipments.show', $shipment)
            ->with('status', __('shipments.updated_success'));
    }

    public function show(Shipment $shipment): View
    {
        $this->authorize('view', $shipment);

        $relations = [
            'statusHistories.changedBy',
            'creator',
            'customer',
            'assignedCourier',
        ];

        if (Schema::hasTable('shipment_evidences')) {
            $relations[] = 'evidences.author';
        }

        $shipment->load($relations);

        $allowedKeys = array_values(array_unique(array_merge(
            [$shipment->status],
            ShipmentTransitionRules::allowedTargets($shipment->status)
        )));

        $statusOptions = collect($allowedKeys)
            ->mapWithKeys(function (string $key) use ($shipment) {
                $label = ShipmentStatus::label($key);
                if ($key === $shipment->status) {
                    $label .= ' ('.__('shipments.status_keep_note').')';
                }

                return [$key => $label];
            })
            ->all();

        return view('shipments.show', [
            'shipment' => $shipment,
            'statusOptions' => $statusOptions,
            'timelineSteps' => $this->buildTimelineSteps($shipment),
            'timelineProgressPercent' => $this->timelineProgressPercent($shipment),
            'timelineCancelled' => $shipment->status === ShipmentStatus::CANCELLED,
        ]);
    }

    public function deactivate(
        Request $request,
        Shipment $shipment,
        ShipmentTransitionService $transitionService
    ): RedirectResponse {
        $this->authorize('deactivate', $shipment);

        if ($shipment->status === ShipmentStatus::DELIVERED) {
            return redirect()
                ->route('shipments.show', $shipment)
                ->withErrors(__('shipments.deactivate_not_allowed_delivered'));
        }

        DB::transaction(function () use ($shipment, $transitionService, $request): void {
            if ($shipment->status !== ShipmentStatus::CANCELLED) {
                $transitionService->transitionTo(
                    $shipment,
                    ShipmentStatus::CANCELLED,
                    __('shipments.deactivate_system_note'),
                    $request->user()
                );
                $shipment->refresh();
            }
        });

        ActivityLogger::log(
            $request->user(),
            AuditActions::SHIPMENT_DEACTIVATED,
            __('audit.shipment_deactivated', ['tracking' => $shipment->tracking_number]),
            $shipment,
            ['action' => 'cancel_status']
        );

        return redirect()
            ->route('shipments.index')
            ->with('status', __('shipments.deactivated_success'));
    }

    public function reportPdf(Shipment $shipment): Response
    {
        $this->authorize('viewReport', $shipment);

        if (! Schema::hasTable('shipment_evidences')) {
            return redirect()
                ->route('shipments.show', $shipment)
                ->withErrors(__('shipments.evidence_table_missing'));
        }

        $shipment->load([
            'organization',
            'customer',
            'statusHistories.changedBy',
            'evidences.author',
        ]);

        $gdEnabled = extension_loaded('gd');
        $mimeByExtension = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        $evidenceImages = [];
        foreach ($shipment->evidences->sortBy('created_at') as $evidence) {
            if ($evidence->image_path) {
                $fullPath = storage_path('app/public/'.$evidence->image_path);
                if (is_readable($fullPath)) {
                    $ext = strtolower((string) pathinfo($fullPath, PATHINFO_EXTENSION));
                    $mime = $mimeByExtension[$ext] ?? null;

                    if ($mime === null) {
                        continue;
                    }

                    // DOMPDF can require GD for some image formats in some environments.
                    // Keep PDF generation stable when GD is unavailable.
                    if (! $gdEnabled && $mime !== 'image/jpeg') {
                        continue;
                    }

                    $evidenceImages[$evidence->id] = [
                        'data' => base64_encode((string) file_get_contents($fullPath)),
                        'mime' => $mime,
                    ];
                }
            }
        }

        $pdf = Pdf::loadView('shipments.pdf.report', [
            'shipment' => $shipment,
            'evidenceImages' => $evidenceImages,
            'statusRows' => $shipment->statusHistories->sortBy('created_at')->values(),
            'evidenceRows' => $shipment->evidences->sortBy('created_at')->values(),
            'gdEnabled' => $gdEnabled,
            'printedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('informe-envio-'.$shipment->tracking_number.'.pdf');
    }

    public function reportExcel(Shipment $shipment): BinaryFileResponse
    {
        $this->authorize('viewReport', $shipment);

        if (! Schema::hasTable('shipment_evidences')) {
            abort(422, __('shipments.evidence_table_missing'));
        }

        $shipment->loadMissing(['statusHistories.changedBy', 'evidences.author']);

        return Excel::download(
            new ShipmentHistoryExport($shipment),
            'historial-envio-'.$shipment->tracking_number.'.xlsx'
        );
    }

    public function guide(Shipment $shipment): View
    {
        $this->authorize('viewGuide', $shipment);

        $shipment->load('organization');

        $trackingUrl = route('tracking.public', [
            'organization_slug' => $shipment->organization->slug,
            'tracking_number' => $shipment->tracking_number,
        ]);

        return view('shipments.guide', [
            'shipment' => $shipment,
            'trackingUrl' => $trackingUrl,
            'qrDataUri' => $this->makeQrDataUri($trackingUrl),
            'printedAt' => now(),
        ]);
    }

    public function guidePdf(Shipment $shipment): Response
    {
        $this->authorize('viewGuide', $shipment);

        $shipment->load('organization');

        $trackingUrl = route('tracking.public', [
            'organization_slug' => $shipment->organization->slug,
            'tracking_number' => $shipment->tracking_number,
        ]);

        $pdf = Pdf::loadView('shipments.pdf.guide', [
            'shipment' => $shipment,
            'trackingUrl' => $trackingUrl,
            'qrDataUri' => $this->makeQrDataUri($trackingUrl),
            'printedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('guia-'.$shipment->tracking_number.'.pdf');
    }

    /**
     * @return list<array{key: string, label: string, phase: string}>
     */
    private function buildTimelineSteps(Shipment $shipment): array
    {
        if ($shipment->status === ShipmentStatus::CANCELLED) {
            return [];
        }

        $keys = [
            ShipmentStatus::RECEIVED,
            ShipmentStatus::IN_TRANSIT,
            ShipmentStatus::OUT_FOR_DELIVERY,
            ShipmentStatus::DELIVERED,
        ];

        $rank = $this->resolveTimelineRank($shipment);
        $isDelivered = $shipment->status === ShipmentStatus::DELIVERED;
        $isIncident = $shipment->status === ShipmentStatus::INCIDENT;

        $steps = [];

        foreach ($keys as $i => $key) {
            $n = $i + 1;

            if ($isDelivered) {
                $phase = 'done';
            } elseif ($isIncident) {
                if ($n < $rank) {
                    $phase = 'done';
                } elseif ($n === $rank) {
                    $phase = 'incident';
                } else {
                    $phase = 'pending';
                }
            } elseif ($n < $rank) {
                $phase = 'done';
            } elseif ($n === $rank) {
                $phase = 'current';
            } else {
                $phase = 'pending';
            }

            $steps[] = [
                'key' => $key,
                'label' => ShipmentStatus::label($key),
                'phase' => $phase,
            ];
        }

        return $steps;
    }

    private function timelineProgressPercent(Shipment $shipment): int
    {
        if ($shipment->status === ShipmentStatus::CANCELLED) {
            return 0;
        }

        if ($shipment->status === ShipmentStatus::DELIVERED) {
            return 100;
        }

        $rank = $this->resolveTimelineRank($shipment);

        return (int) min(100, max(5, round(($rank / 4) * 100)));
    }

    private function resolveTimelineRank(Shipment $shipment): int
    {
        if ($shipment->status === ShipmentStatus::CANCELLED) {
            return 0;
        }

        $mainFlow = [
            ShipmentStatus::RECEIVED,
            ShipmentStatus::IN_TRANSIT,
            ShipmentStatus::OUT_FOR_DELIVERY,
            ShipmentStatus::DELIVERED,
        ];

        if ($shipment->status === ShipmentStatus::INCIDENT) {
            foreach ($shipment->statusHistories->sortByDesc('created_at') as $history) {
                $idx = array_search($history->to_status, $mainFlow, true);
                if ($idx !== false) {
                    return $idx + 1;
                }
            }

            return 1;
        }

        $idx = array_search($shipment->status, $mainFlow, true);

        return $idx !== false ? $idx + 1 : 1;
    }

    private function makeQrDataUri(string $payload): string
    {
        $qrCode = QrCode::create($payload)->setSize(140)->setMargin(4);
        $writer = new SvgWriter();
        $result = $writer->write($qrCode);

        return 'data:image/svg+xml;base64,'.base64_encode($result->getString());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    private function messengersForTenant(): \Illuminate\Database\Eloquent\Collection
    {
        $orgId = tenant_id();
        if ($orgId === null) {
            return User::query()->whereRaw('1 = 0')->get();
        }

        return User::query()
            ->join('organization_user', 'users.id', '=', 'organization_user.user_id')
            ->where('organization_user.organization_id', $orgId)
            ->where('organization_user.role', OrganizationRole::MENSAJERO)
            ->where('organization_user.is_active', true)
            ->select('users.*')
            ->orderBy('users.name')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function attributesFromShipmentForm(array $validated): array
    {
        $mode = $validated['customer_mode'];

        if ($mode === 'skip') {
            $validated['customer_id'] = null;
        }

        if ($mode === 'existing' || $mode === 'new') {
            $customerId = (int) $validated['customer_id'];
            $customer = Customer::query()->findOrFail($customerId);
            if (! $customer->is_active) {
                abort(422, __('shipments.customer_inactive_for_shipment'));
            }
            $validated['recipient_name'] = $customer->name;
            $validated['recipient_phone'] = $customer->phone;
            $validated['recipient_email'] = $customer->email;

            if (! empty($validated['customer_address_id'])) {
                $addr = CustomerAddress::query()->findOrFail((int) $validated['customer_address_id']);
                if ((int) $addr->customer_id !== $customerId) {
                    abort(422);
                }
                $validated['destination_address_line'] = $addr->address_line;
                if ($addr->city_id && $addr->department_id) {
                    $city = City::query()->with('department')->findOrFail($addr->city_id);
                    $validated['destination_city_id'] = $city->id;
                    $validated['destination_department_id'] = $city->department_id;
                }
            }
        }

        $originCity = City::query()
            ->with('department')
            ->findOrFail($validated['origin_city_id']);

        $destinationCity = City::query()
            ->with('department')
            ->findOrFail($validated['destination_city_id']);

        return [
            'customer_id' => $mode === 'skip' ? null : ($validated['customer_id'] ?? null),
            'assigned_user_id' => $validated['assigned_user_id'] ?? null,
            'sender_name' => $validated['sender_name'],
            'sender_phone' => $validated['sender_phone'],
            'sender_email' => $validated['sender_email'] ?? null,
            'recipient_name' => $validated['recipient_name'],
            'recipient_phone' => $validated['recipient_phone'],
            'recipient_email' => $validated['recipient_email'] ?? null,
            'origin_address_line' => $validated['origin_address_line'],
            'origin_city' => $originCity->name,
            'origin_region' => $originCity->department->name,
            'origin_postal_code' => $validated['origin_postal_code'] ?? null,
            'origin_department_id' => $validated['origin_department_id'],
            'origin_city_id' => $validated['origin_city_id'],
            'destination_address_line' => $validated['destination_address_line'],
            'destination_city' => $destinationCity->name,
            'destination_region' => $destinationCity->department->name,
            'destination_postal_code' => $validated['destination_postal_code'] ?? null,
            'destination_department_id' => $validated['destination_department_id'],
            'destination_city_id' => $validated['destination_city_id'],
            'reference_internal' => $validated['reference_internal'] ?? null,
            'notes_internal' => $validated['notes_internal'] ?? null,
            'weight_kg' => $validated['weight_kg'] ?? null,
            'declared_value' => $validated['declared_value'] ?? null,
            'service_type' => $validated['service_type'] ?? ServiceType::STANDARD,
            'distance_km' => $validated['distance_km'] ?? null,
            'payment_type' => $validated['payment_type'] ?? PaymentType::CREDIT,
        ];
    }

    public function updatePayment(
        UpdateShipmentPaymentRequest $request,
        Shipment $shipment
    ): RedirectResponse {
        $this->authorize('updatePayment', $shipment);

        $validated = $request->validated();
        $before = $shipment->only(['payment_status', 'paid_amount', 'payment_date']);
        $status = $validated['payment_status'];

        $shipment->payment_status = $status;

        if ($status === PaymentStatus::PAID) {
            $amount = $validated['paid_amount'] ?? null;
            $shipment->paid_amount = $amount !== null && $amount !== ''
                ? $amount
                : ($shipment->cost ?? 0);
            $shipment->payment_date = isset($validated['payment_date'])
                ? \Carbon\Carbon::parse($validated['payment_date'])->startOfDay()
                : now()->startOfDay();
        } else {
            $shipment->paid_amount = $validated['paid_amount'] ?? null;
            $shipment->payment_date = isset($validated['payment_date'])
                ? \Carbon\Carbon::parse($validated['payment_date'])->startOfDay()
                : null;
        }

        $shipment->save();

        ActivityLogger::log(
            $request->user(),
            AuditActions::SHIPMENT_PAYMENT_CHANGED,
            __('finance.payment_updated'),
            $shipment,
            $this->auditMetaPayload(array_keys($before), $before, $shipment->only(array_keys($before)), (int) $shipment->id)
        );

        return redirect()
            ->route('shipments.show', $shipment)
            ->with('status', __('finance.payment_updated'));
    }

    private function resolveShipmentCost(
        int $organizationId,
        string $serviceType,
        mixed $weightKg,
        mixed $distanceKm
    ): ?float {
        $rate = $this->resolveShipmentRate($organizationId, $serviceType);

        return app(ShipmentCostCalculator::class)->calculate($rate, $weightKg, $distanceKm);
    }

    public function estimateCost(Request $request): JsonResponse
    {
        $this->authorize('create', Shipment::class);

        $tenantId = tenant_id();
        if ($tenantId === null) {
            abort(403);
        }

        $serviceType = (string) $request->input('service_type', ServiceType::STANDARD);
        if (! in_array($serviceType, ServiceType::all(), true)) {
            $serviceType = ServiceType::STANDARD;
        }

        $rate = $this->resolveShipmentRate($tenantId, $serviceType);
        $cost = app(ShipmentCostCalculator::class)->calculate(
            $rate,
            $request->input('weight_kg'),
            $request->input('distance_km')
        );

        return response()->json([
            'cost' => $cost,
            'service_rate_id' => $rate?->id,
            'formatted' => $cost !== null ? '$'.number_format((float) $cost, 2, ',', '.') : null,
        ]);
    }

    private function resolveShipmentRate(int $organizationId, string $serviceType): ?ServiceRate
    {
        return ServiceRate::query()
            ->where('organization_id', $organizationId)
            ->where('service_type', $serviceType)
            ->where('active', true)
            ->first();
    }

    /**
     * @param  array<string, mixed>  $before
     */
    private function logShipmentSpecificChanges(Request $request, Shipment $shipment, array $before): void
    {
        $after = $shipment->only(array_keys($before));

        if ((int) ($before['service_rate_id'] ?? 0) !== (int) ($after['service_rate_id'] ?? 0)) {
            ActivityLogger::log(
                $request->user(),
                AuditActions::SHIPMENT_RATE_CHANGED,
                __('audit.shipment_updated', ['tracking' => $shipment->tracking_number]),
                $shipment,
                $this->auditMetaPayload(['service_rate_id'], $before, $after, (int) $shipment->id)
            );
        }

        if ((float) ($before['cost'] ?? 0) !== (float) ($after['cost'] ?? 0)) {
            ActivityLogger::log(
                $request->user(),
                AuditActions::SHIPMENT_COST_CHANGED,
                __('audit.shipment_updated', ['tracking' => $shipment->tracking_number]),
                $shipment,
                $this->auditMetaPayload(['cost'], $before, $after, (int) $shipment->id)
            );
        }
    }

    /**
     * @param  array<int, string>  $fields
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @return array<string, mixed>
     */
    private function auditMetaPayload(array $fields, array $before, array $after, ?int $recordId = null): array
    {
        $old = [];
        $new = [];
        foreach ($fields as $field) {
            $old[$field] = $before[$field] ?? null;
            $new[$field] = $after[$field] ?? null;
        }

        return [
            'model' => Shipment::class,
            'record_id' => $recordId,
            'changes' => [
                'before' => $old,
                'after' => $new,
            ],
        ];
    }

    public function updateStatus(
        UpdateShipmentStatusRequest $request,
        Shipment $shipment,
        ShipmentTransitionService $transitionService
    ): RedirectResponse {
        $this->authorize('updateStatus', $shipment);

        $validated = $request->validated();
        $from = $shipment->status;

        $transitionService->transitionTo(
            $shipment,
            $validated['status'],
            $validated['notes'] ?? null,
            $request->user()
        );

        $shipment->refresh();

        if ($from === $validated['status']) {
            ActivityLogger::log(
                $request->user(),
                AuditActions::SHIPMENT_COURIER_NOTE,
                (string) ($validated['notes'] ?? ''),
                $shipment
            );
        } else {
            $desc = __('audit.shipment_status_changed', [
                'from' => ShipmentStatus::label($from),
                'to' => ShipmentStatus::label($validated['status']),
            ]);
            $note = trim((string) ($validated['notes'] ?? ''));
            if ($note !== '') {
                $desc .= ' — '.$note;
            }
            ActivityLogger::log(
                $request->user(),
                AuditActions::SHIPMENT_STATUS_CHANGED,
                $desc,
                $shipment,
                $this->auditMetaPayload(
                    ['status', 'status_note'],
                    ['status' => $from, 'status_note' => null],
                    ['status' => $validated['status'], 'status_note' => $validated['notes'] ?? null],
                    (int) $shipment->id
                )
            );
        }

        return redirect()
            ->route('shipments.show', $shipment)
            ->with('status', __('shipments.status_updated'));
    }
}
