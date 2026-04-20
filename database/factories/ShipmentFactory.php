<?php

namespace Database\Factories;

use App\Finance\PaymentStatus;
use App\Finance\PaymentType;
use App\Finance\ServiceType;
use App\Models\City;
use App\Models\Organization;
use App\Models\Shipment;
use App\Shipments\ShipmentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        $originCity = City::factory()->create();
        $destinationCity = City::factory()->create();

        return [
            'organization_id' => Organization::factory(),
            'tracking_number' => 'RT-2099-'.$this->faker->unique()->numerify('######'),
            'sender_name' => $this->faker->name(),
            'sender_phone' => $this->faker->numerify('3##########'),
            'sender_email' => $this->faker->optional()->safeEmail(),
            'recipient_name' => $this->faker->name(),
            'recipient_phone' => $this->faker->numerify('3##########'),
            'recipient_email' => $this->faker->optional()->safeEmail(),
            'origin_address_line' => $this->faker->streetAddress(),
            'origin_department_id' => $originCity->department_id,
            'origin_city_id' => $originCity->id,
            'origin_city' => $originCity->name,
            'origin_region' => $originCity->department->name,
            'origin_postal_code' => null,
            'destination_address_line' => $this->faker->streetAddress(),
            'destination_department_id' => $destinationCity->department_id,
            'destination_city_id' => $destinationCity->id,
            'destination_city' => $destinationCity->name,
            'destination_region' => $destinationCity->department->name,
            'destination_postal_code' => null,
            'reference_internal' => null,
            'notes_internal' => null,
            'weight_kg' => null,
            'declared_value' => null,
            'service_type' => ServiceType::STANDARD,
            'distance_km' => null,
            'cost' => null,
            'payment_type' => PaymentType::CREDIT,
            'payment_status' => PaymentStatus::PENDING,
            'paid_amount' => null,
            'payment_date' => null,
            'status' => ShipmentStatus::RECEIVED,
            'created_by_user_id' => null,
        ];
    }
}
