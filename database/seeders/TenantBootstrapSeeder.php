<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use App\Organizations\OrganizationRole;
use App\Support\DefaultOrganization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantBootstrapSeeder extends Seeder
{
    /**
     * Organización inicial y usuario administrador para desarrollo local.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $organization = Organization::query()->firstOrCreate(
                ['slug' => DefaultOrganization::SLUG],
                ['name' => DefaultOrganization::NAME],
            );

            $admin = User::query()->firstOrCreate(
                ['email' => 'andresrojas@rojastech.com.co'],
                [
                    'name' => 'Andres Rojas',
                    'password' => Hash::make('12345678'),
                ]
            );

            if (! $admin->belongsToOrganization((int) $organization->id)) {
                $admin->organizations()->attach($organization->id, [
                    'role' => OrganizationRole::ADMIN,
                    'is_active' => true,
                ]);
            }
        });
    }
}
