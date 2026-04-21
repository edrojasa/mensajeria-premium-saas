<?php

namespace App\Http\Requests;

use App\Finance\PaymentType;
use App\Finance\ServiceType;
use App\Models\CustomerAddress;
use App\Models\Shipment;
use App\Organizations\OrganizationRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Shipment $shipment */
        $shipment = $this->route('shipment');

        return $this->user() !== null
            && $this->user()->can('update', $shipment);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $tenantId = tenant_id();

        return [
            'customer_mode' => ['required', 'string', 'in:skip,existing,new'],

            'customer_id' => [
                'nullable',
                'integer',
                Rule::exists('customers', 'id')->where(
                    fn ($q) => $q->where('organization_id', $tenantId)->where('is_active', true)
                ),
            ],
            'customer_address_id' => ['nullable', 'integer'],

            'new_customer_name' => ['nullable', 'string', 'max:255'],
            'new_customer_document' => ['nullable', 'string', 'max:64'],
            'new_customer_phone' => ['nullable', 'string', 'max:32'],
            'new_customer_email' => ['nullable', 'email', 'max:255'],
            'new_customer_notes' => ['nullable', 'string', 'max:5000'],

            'assigned_user_id' => [
                'nullable',
                'integer',
                Rule::exists('organization_user', 'user_id')->where(
                    fn ($q) => $q
                        ->where('organization_id', $tenantId)
                        ->where('role', OrganizationRole::MENSAJERO)
                        ->where('is_active', true)
                ),
            ],

            'sender_name' => ['required', 'string', 'max:255'],
            'sender_phone' => ['required', 'string', 'max:32'],
            'sender_email' => ['nullable', 'email', 'max:255'],

            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'max:32'],
            'recipient_email' => ['nullable', 'email', 'max:255'],

            'origin_address_line' => ['required', 'string', 'max:500'],
            'origin_department_id' => ['required', 'integer', 'exists:departments,id'],
            'origin_city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(
                    fn ($query) => $query->where('department_id', (int) $this->input('origin_department_id'))
                ),
            ],
            'origin_postal_code' => ['nullable', 'string', 'max:32'],

            'destination_address_line' => ['required', 'string', 'max:500'],
            'destination_department_id' => ['required', 'integer', 'exists:departments,id'],
            'destination_city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(
                    fn ($query) => $query->where(
                        'department_id',
                        (int) $this->input('destination_department_id')
                    )
                ),
            ],
            'destination_postal_code' => ['nullable', 'string', 'max:32'],

            'reference_internal' => ['nullable', 'string', 'max:120'],
            'notes_internal' => ['nullable', 'string', 'max:5000'],
            'weight_kg' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'declared_value' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],

            'service_type' => ['nullable', 'string', Rule::in(ServiceType::all())],
            'distance_km' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'payment_type' => ['nullable', 'string', Rule::in(PaymentType::all())],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $mode = $this->input('customer_mode');

            if ($mode === 'existing' && ! $this->filled('customer_id')) {
                $validator->errors()->add('customer_id', __('validation.required', ['attribute' => 'cliente']));
            }

            if ($mode === 'new') {
                if (! $this->filled('new_customer_name')) {
                    $validator->errors()->add('new_customer_name', __('validation.required', ['attribute' => __('customers.field_name')]));
                }
                if (! $this->filled('new_customer_phone')) {
                    $validator->errors()->add('new_customer_phone', __('validation.required', ['attribute' => __('customers.field_phone')]));
                }
            }

            if ($this->filled('customer_address_id') && $this->filled('customer_id')) {
                $addr = CustomerAddress::query()->find($this->input('customer_address_id'));
                if ($addr === null || (int) $addr->customer_id !== (int) $this->input('customer_id')) {
                    $validator->errors()->add('customer_address_id', __('customers.address_invalid_for_customer'));
                }
            }
        });
    }
}
