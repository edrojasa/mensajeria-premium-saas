<?php

namespace App\Http\Requests;

use App\Models\Shipment;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentEvidenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Shipment $shipment */
        $shipment = $this->route('shipment');

        return $this->user() !== null && $this->user()->can('addEvidence', $shipment);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'note' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->hasFile('image') && ! $this->filled('note')) {
                $validator->errors()->add('note', __('shipments.evidence_note_or_image_required'));
            }
        });
    }
}
