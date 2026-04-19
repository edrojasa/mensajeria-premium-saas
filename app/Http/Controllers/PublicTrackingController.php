<?php

namespace App\Http\Controllers;

use App\Http\Requests\LookupPublicTrackingRequest;
use App\Models\Organization;
use App\Models\Shipment;
use App\Shipments\ShipmentStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Seguimiento público sin autenticación (solo datos no sensibles).
 */
class PublicTrackingController extends Controller
{
    public function search(): View
    {
        return view('tracking.search');
    }

    public function lookup(LookupPublicTrackingRequest $request): RedirectResponse
    {
        $trackingNumber = $request->validated('tracking_number');

        $query = Shipment::withoutGlobalScopes()
            ->where('tracking_number', $trackingNumber);

        if ($request->filled('organization_slug')) {
            $organization = Organization::query()
                ->where('slug', $request->validated('organization_slug'))
                ->firstOrFail();

            $query->where('organization_id', $organization->id);
        }

        $matches = $query->get();

        if ($matches->isEmpty()) {
            return redirect()
                ->route('tracking.search')
                ->withInput()
                ->withErrors(['tracking_number' => __('tracking.not_found')]);
        }

        if ($matches->count() > 1 && ! $request->filled('organization_slug')) {
            return redirect()
                ->route('tracking.search')
                ->withInput()
                ->withErrors(['tracking_number' => __('tracking.specify_organization')]);
        }

        /** @var Shipment $shipment */
        $shipment = $matches->first();
        $shipment->loadMissing('organization');

        return redirect()->route('tracking.public', [
            'organization_slug' => $shipment->organization->slug,
            'tracking_number' => $shipment->tracking_number,
        ]);
    }

    public function show(string $organizationSlug, string $trackingNumber): View
    {
        $organization = Organization::query()
            ->where('slug', $organizationSlug)
            ->firstOrFail();

        $shipment = Shipment::withoutGlobalScopes()
            ->where('organization_id', $organization->id)
            ->where('tracking_number', $trackingNumber)
            ->firstOrFail();

        $shipment->load(['statusHistories' => fn ($q) => $q->orderBy('created_at')]);

        return view('tracking.public-show', [
            'organization' => $organization,
            'shipment' => $shipment,
            'timeline' => $shipment->statusHistories->map(fn ($h) => [
                'at' => $h->created_at,
                'status_key' => $h->to_status,
                'label' => ShipmentStatus::label($h->to_status),
            ]),
        ]);
    }
}
