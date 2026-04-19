<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'document' => fake()->optional()->numerify('########'),
            'phone' => fake()->numerify('3#########'),
            'email' => fake()->optional()->safeEmail(),
            'notes' => null,
        ];
    }
}
