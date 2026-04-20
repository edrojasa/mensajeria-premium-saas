<?php

namespace App\Http\Requests;

use App\Finance\PaymentStatus;
use App\Models\Shipment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShipmentPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Shipment $shipment */
        $shipment = $this->route('shipment');

        return $this->user() !== null
            && $this->user()->can('updatePayment', $shipment);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'payment_status' => ['required', 'string', Rule::in(PaymentStatus::all())],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_date' => ['nullable', 'date'],
        ];
    }
}
