<?php

namespace App\Http\Requests;

use App\Finance\ServiceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessFinancialModule() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $tenantId = tenant_id();

        return [
            'service_type' => [
                'required',
                'string',
                Rule::in(ServiceType::all()),
                Rule::unique('service_rates', 'service_type')->where(
                    fn ($q) => $q->where('organization_id', $tenantId)
                ),
            ],
            'base_price' => ['required', 'numeric', 'min:0'],
            'price_per_kg' => ['nullable', 'numeric', 'min:0'],
            'price_per_km' => ['nullable', 'numeric', 'min:0'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
