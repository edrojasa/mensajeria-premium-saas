<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentStatusRequest;
use App\Models\City;
use App\Models\Department;
use App\Models\Shipment;
use App\Shipments\ShipmentStatus;
use App\Shipments\ShipmentTransitionRules;
use App\Shipments\ShipmentTransitionService;
use App\Shipments\TrackingNumberGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ShipmentController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Shipment::class);

        $shipments = Shipment::query()
            ->latest()
            ->paginate(15);

        return view('shipments.index', compact('shipments'));
    }

    public function create(): View
    {
        $this->authorize('create', Shipment::class);

        $departments = Department::query()
            ->where('country_code', 'CO')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('shipments.create', compact('departments'));
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

        $originCity = City::query()
            ->with('department')
            ->findOrFail($validated['origin_city_id']);

        $destinationCity = City::query()
            ->with('department')
            ->findOrFail($validated['destination_city_id']);

        $validated['origin_city'] = $originCity->name;
        $validated['origin_region'] = $originCity->department->name;
        $validated['destination_city'] = $destinationCity->name;
        $validated['destination_region'] = $destinationCity->department->name;

        $shipment = new Shipment($validated);
        $shipment->tracking_number = $trackingNumberGenerator->generate($tenantId);
        $shipment->status = ShipmentStatus::RECEIVED;
        $shipment->created_by_user_id = $request->user()->id;

        $transitionService->createShipmentWithInitialStatus($shipment, $request->user());

        return redirect()
            ->route('shipments.show', $shipment)
            ->with('status', __('shipments.created_success'));
    }

    public function show(Shipment $shipment): View
    {
        $this->authorize('view', $shipment);

        $shipment->load(['statusHistories.changedBy', 'creator']);

        $allowedKeys = ShipmentTransitionRules::allowedTargets($shipment->status);

        $statusOptions = collect($allowedKeys)
            ->mapWithKeys(fn (string $key) => [$key => ShipmentStatus::label($key)])
            ->all();

        return view('shipments.show', [
            'shipment' => $shipment,
            'statusOptions' => $statusOptions,
            'timelineSteps' => $this->buildTimelineSteps($shipment),
            'timelineProgressPercent' => $this->timelineProgressPercent($shipment),
        ]);
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
        if ($shipment->status === ShipmentStatus::DELIVERED) {
            return 100;
        }

        $rank = $this->resolveTimelineRank($shipment);

        return (int) min(100, max(5, round(($rank / 4) * 100)));
    }

    private function resolveTimelineRank(Shipment $shipment): int
    {
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

    public function updateStatus(
        UpdateShipmentStatusRequest $request,
        Shipment $shipment,
        ShipmentTransitionService $transitionService
    ): RedirectResponse {
        $this->authorize('update', $shipment);

        $transitionService->transitionTo(
            $shipment,
            $request->validated('status'),
            $request->validated('notes'),
            $request->user()
        );

        return redirect()
            ->route('shipments.show', $shipment)
            ->with('status', __('shipments.status_updated'));
    }
}
