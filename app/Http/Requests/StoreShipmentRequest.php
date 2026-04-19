<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShipmentRequest extends FormRequest
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
        return [
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
        ];
    }
}
