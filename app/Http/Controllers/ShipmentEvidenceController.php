<?php

namespace App\Http\Controllers;

use App\Audit\AuditActions;
use App\Http\Requests\StoreShipmentEvidenceRequest;
use App\Models\Shipment;
use App\Models\ShipmentEvidence;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;

class ShipmentEvidenceController extends Controller
{
    public function store(StoreShipmentEvidenceRequest $request, Shipment $shipment): RedirectResponse
    {
        if (! Schema::hasTable('shipment_evidences')) {
            return redirect()
                ->route('shipments.show', $shipment)
                ->withErrors(__('shipments.evidence_table_missing'));
        }

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('shipment-evidences', 'public');
        }

        /** @var ShipmentEvidence $evidence */
        $evidence = $shipment->evidences()->create([
            'user_id' => $request->user()->id,
            'note' => $request->input('note'),
            'image_path' => $path,
        ]);

        ActivityLogger::log(
            $request->user(),
            AuditActions::SHIPMENT_EVIDENCE_UPLOADED,
            __('audit.shipment_evidence_uploaded', ['tracking' => $shipment->tracking_number]),
            $evidence,
            [
                'model' => Shipment::class,
                'record_id' => $shipment->id,
                'changes' => [
                    'before' => [],
                    'after' => [
                        'evidence_id' => $evidence->id,
                        'note' => $evidence->note,
                        'has_image' => $path !== null,
                    ],
                ],
            ]
        );

        return redirect()
            ->route('shipments.show', $shipment)
            ->with('status', __('shipments.evidence_saved'));
    }
}
