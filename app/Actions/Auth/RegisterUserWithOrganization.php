<?php

namespace App\Actions\Auth;

use App\Models\Organization;
use App\Models\User;
use App\Organizations\OrganizationRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterUserWithOrganization
{
    public function __invoke(string $userName, string $email, string $password, string $organizationName): User
    {
        return DB::transaction(function () use ($userName, $email, $password, $organizationName) {
            $user = User::create([
                'name' => $userName,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $organization = Organization::create([
                'name' => $organizationName,
                'slug' => $this->uniqueSlug($organizationName),
            ]);

            $user->organizations()->attach($organization->id, [
                'role' => OrganizationRole::ADMIN,
                'is_active' => true,
            ]);

            return $user->fresh();
        });
    }

    private function uniqueSlug(string $organizationName): string
    {
        $base = Str::slug($organizationName);
        if ($base === '') {
            $base = 'organizacion';
        }

        $slug = $base;
        $n = 0;

        while (Organization::where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$n);
        }

        return $slug;
    }
}
