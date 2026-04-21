<?php

namespace App\Http\Requests;

use App\Models\Shipment;
use App\Shipments\ShipmentStatus;
use App\Shipments\ShipmentTransitionRules;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShipmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Shipment $shipment */
        $shipment = $this->route('shipment');

        return $this->user() !== null && $this->user()->can('updateStatus', $shipment);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Shipment $shipment */
        $shipment = $this->route('shipment');

        $allowed = array_values(array_unique(array_merge(
            [$shipment->status],
            ShipmentTransitionRules::allowedTargets($shipment->status)
        )));

        if ($this->user()->isMessenger()) {
            $allowed = array_values(array_filter(
                $allowed,
                fn (string $s) => $s !== ShipmentStatus::CANCELLED
            ));
        }

        return [
            'status' => [
                'required',
                'string',
                Rule::in($allowed),
            ],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Shipment $shipment */
            $shipment = $this->route('shipment');

            $newStatus = (string) $this->input('status');
            $notesTrim = trim((string) ($this->input('notes') ?? ''));

            if ($newStatus === $shipment->status && $notesTrim === '') {
                $validator->errors()->add('notes', __('shipments.note_or_status_required'));
            }
        });
    }
}
