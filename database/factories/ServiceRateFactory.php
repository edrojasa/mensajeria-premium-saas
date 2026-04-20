<?php

namespace Database\Factories;

use App\Finance\ServiceType;
use App\Models\Organization;
use App\Models\ServiceRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRate>
 */
class ServiceRateFactory extends Factory
{
    protected $model = ServiceRate::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'service_type' => ServiceType::STANDARD,
            'base_price' => fake()->randomFloat(2, 8000, 35000),
            'price_per_kg' => fake()->optional()->randomFloat(4, 500, 3000),
            'price_per_km' => fake()->optional()->randomFloat(4, 200, 1500),
            'active' => true,
        ];
    }
}
