<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LookupPublicTrackingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tracking_number' => ['required', 'string', 'max:64'],
            'organization_slug' => ['nullable', 'string', 'max:255', 'exists:organizations,slug'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $tracking = $this->input('tracking_number');
        $slug = $this->input('organization_slug');

        $this->merge([
            'tracking_number' => is_string($tracking) ? trim($tracking) : $tracking,
            'organization_slug' => is_string($slug) && trim($slug) !== ''
                ? trim($slug)
                : null,
        ]);
    }
}
