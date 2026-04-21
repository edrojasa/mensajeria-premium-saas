<?php

namespace App\Http\Requests;

use App\Models\City;
use App\Models\Customer;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('create', Customer::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:64'],
            'phone' => ['required', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'addresses' => ['nullable', 'array'],
            'addresses.*.label' => ['nullable', 'string', 'max:64'],
            'addresses.*.address_line' => ['nullable', 'string', 'max:500'],
            'addresses.*.department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'addresses.*.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'addresses.*.reference_notes' => ['nullable', 'string', 'max:500'],
            'addresses.*.is_default' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->input('addresses', []) as $idx => $row) {
                if (! $this->addressRowHasAnyInput($row)) {
                    continue;
                }

                if (empty($row['label'])) {
                    $validator->errors()->add(
                        "addresses.$idx.label",
                        __('validation.required', ['attribute' => __('customers.address_label')])
                    );
                }
                if (empty($row['address_line'])) {
                    $validator->errors()->add(
                        "addresses.$idx.address_line",
                        __('validation.required', ['attribute' => __('customers.address_line')])
                    );
                }
                if (empty($row['department_id'])) {
                    $validator->errors()->add(
                        "addresses.$idx.department_id",
                        __('validation.required', ['attribute' => __('shipments.department')])
                    );
                }
                if (empty($row['city_id'])) {
                    $validator->errors()->add(
                        "addresses.$idx.city_id",
                        __('validation.required', ['attribute' => __('shipments.city')])
                    );
                }

                if (! empty($row['city_id']) && ! empty($row['department_id'])) {
                    $city = City::query()->find($row['city_id']);
                    if ($city === null || (int) $city->department_id !== (int) $row['department_id']) {
                        $validator->errors()->add(
                            "addresses.$idx.city_id",
                            __('customers.validation_city_department_mismatch')
                        );
                    }
                }
            }
        });
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function addressRowHasAnyInput(array $row): bool
    {
        foreach (['label', 'address_line', 'department_id', 'city_id', 'reference_notes'] as $key) {
            $v = $row[$key] ?? null;
            if ($v !== null && $v !== '') {
                return true;
            }
        }

        return ! empty($row['is_default']);
    }
}
