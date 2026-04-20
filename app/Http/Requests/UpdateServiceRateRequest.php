<?php

namespace App\Http\Requests;

use App\Models\ServiceRate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var ServiceRate $rate */
        $rate = $this->route('service_rate');

        return $this->user() !== null
            && $this->user()->can('update', $rate);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'base_price' => ['required', 'numeric', 'min:0'],
            'price_per_kg' => ['nullable', 'numeric', 'min:0'],
            'price_per_km' => ['nullable', 'numeric', 'min:0'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
