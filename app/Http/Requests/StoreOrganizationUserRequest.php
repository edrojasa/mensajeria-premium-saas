<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Organizations\OrganizationRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class StoreOrganizationUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageOrganizationUsers() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:32'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(OrganizationRole::ALL)],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
