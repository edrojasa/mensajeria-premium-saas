<?php

namespace Database\Factories;

use App\Enums\UserAccountStatus;
use App\Models\User;
use App\Organizations\OrganizationRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'status' => UserAccountStatus::ACTIVE,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Attach a freshly created organization (tests / seeding).
     *
     * @return static
     */
    public function withOrganization(string $role = OrganizationRole::ADMIN)
    {
        return $this->afterCreating(function (User $user) use ($role) {
            $organization = \App\Models\Organization::factory()->create();
            $user->organizations()->attach($organization->id, [
                'role' => $role,
                'is_active' => true,
            ]);
        });
    }
}
