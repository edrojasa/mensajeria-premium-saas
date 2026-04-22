<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Organizations\OrganizationRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateOrganizationUserRequest extends FormRequest
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
        /** @var User $target */
        $target = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($target->id),
            ],
            'phone' => ['nullable', 'string', 'max:32'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(OrganizationRole::ALL)],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
