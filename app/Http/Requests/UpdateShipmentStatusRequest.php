<?php

namespace App\Http\Requests;

use App\Models\Shipment;
use App\Shipments\ShipmentTransitionRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShipmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Shipment $shipment */
        $shipment = $this->route('shipment');

        return [
            'status' => [
                'required',
                'string',
                Rule::in(ShipmentTransitionRules::allowedTargets($shipment->status)),
            ],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
